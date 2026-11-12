<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\StartsSession;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\RestrictedTester;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SessionControllerTest extends TestCase
{
    #[Test]
    public function it_starts_a_session(): void
    {
        $tester = Tester::query()->create();
        $scenario = Scenario::factory()->active()->create();
        $session = Session::factory()->for($tester, 'tester')->for($scenario)->create();

        $this->mock(StartsSession::class, function (MockInterface $mock) use ($tester, $scenario, $session): void {
            $mock->shouldReceive('start')
                ->withArgs(fn (Tester $givenTester, Scenario $givenScenario): bool => $givenTester->is($tester)
                    && $givenScenario->is($scenario))
                ->once()
                ->andReturn($session);
        });

        $this
            ->actingAs($tester)
            ->post(route('uat.sessions.store', $scenario))
            ->assertRedirect(route('uat.sessions.show', $session));
    }

    #[Test]
    public function it_forbids_scenarios_the_tester_may_not_test(): void
    {
        $scenario = Scenario::factory()->active()->create();

        $this
            ->actingAs(RestrictedTester::query()->create())
            ->post(route('uat.sessions.store', $scenario))
            ->assertForbidden();
    }

    #[Test]
    #[DataProvider('steps')]
    public function it_shows_a_session(array $query, string $shown, string $hidden): void
    {
        $tester = Tester::query()->create();

        $scenario = Scenario::factory()->active()->create(['start_url' => 'https://example.com/start']);
        ScenarioTranslation::factory()->create([
            'scenario_id' => $scenario->id,
            'title' => '::title::',
            'description' => '::description::',
            'preconditions' => '::preconditions::',
        ]);

        $session = Session::factory()->for($tester, 'tester')->for($scenario)->create();
        SessionStep::factory()->for($session)->create([
            'position' => 1,
            'instruction' => '::first-instruction::',
            'verdict' => Verdict::Accepted,
        ]);
        SessionStep::factory()->for($session)->create([
            'position' => 2,
            'instruction' => '::second-instruction::',
            'url' => 'https://example.com/orders',
            'link_label' => '::link-label::',
        ]);

        $this
            ->actingAs($tester)
            ->get(route('uat.sessions.show', [$session, ...$query]))
            ->assertOk()
            ->assertSeeInOrder([
                '::title::',
                '::description::',
                '::preconditions::',
                'https://example.com/start',
                $shown,
                'Feature works as intended',
                'Step 2 of 2',
            ])
            ->assertDontSee($hidden);
    }

    public static function steps(): Iterator
    {
        yield 'current step' => [[], '::second-instruction::', '::first-instruction::'];
        yield 'earlier step' => [['step' => 1], '::first-instruction::', '::second-instruction::'];
    }

    #[Test]
    public function it_shows_the_summary(): void
    {
        $tester = Tester::query()->create();

        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id]);

        $session = Session::factory()->for($tester, 'tester')->for($scenario)->create();
        SessionStep::factory()->for($session)->create(['position' => 1, 'verdict' => Verdict::Accepted]);
        SessionStep::factory()->for($session)->create(['position' => 2, 'verdict' => Verdict::Feedback]);

        $this
            ->actingAs($tester)
            ->get(route('uat.sessions.show', $session))
            ->assertOk()
            ->assertSeeInOrder(['1 accepted, 1 with feedback', 'Finish'])
            ->assertDontSee('Feature works as intended');
    }

    #[Test]
    public function it_forbids_sessions_of_other_testers(): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        $this
            ->actingAs(Tester::query()->create())
            ->get(route('uat.sessions.show', $session))
            ->assertForbidden();
    }
}
