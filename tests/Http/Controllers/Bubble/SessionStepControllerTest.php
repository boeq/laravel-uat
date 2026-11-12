<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\JudgesStep;
use Boeq\Uat\Data\VerdictData;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

final class SessionStepControllerTest extends TestCase
{
    #[Test]
    public function it_saves_a_verdict(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();
        $step = SessionStep::factory()->for($session)->create();

        $this->mock(JudgesStep::class, function (MockInterface $mock) use ($step): void {
            $mock->shouldReceive('judge')
                ->withArgs(fn (SessionStep $given, VerdictData $data): bool => $given->is($step)
                    && $data->toArray() === [
                        'verdict' => Verdict::Feedback,
                        'feedback' => 'The order form stays empty.',
                        'page_url' => 'https://example.com/orders',
                        'viewport_width' => 1280,
                        'viewport_height' => 720,
                        'user_agent' => '::user-agent::',
                    ])
                ->once();
        });

        $this
            ->actingAs($tester)
            ->withHeader('User-Agent', '::user-agent::')
            ->put(route('uat.sessions.steps.update', [$session, $step]), [
                'verdict' => 'feedback',
                'feedback' => 'The order form stays empty.',
                'page_url' => 'https://example.com/orders',
                'viewport_width' => '1280',
                'viewport_height' => '720',
            ])
            ->assertRedirect(route('uat.sessions.show', $session));
    }

    #[Test]
    public function it_requires_a_description_with_feedback(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();
        $step = SessionStep::factory()->for($session)->create();

        $this
            ->actingAs($tester)
            ->putJson(route('uat.sessions.steps.update', [$session, $step]), [
                'verdict' => 'feedback',
                'feedback' => 'bug',
                'page_url' => 'https://example.com/orders',
                'viewport_width' => 1280,
                'viewport_height' => 720,
            ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Describe what you did, what happened and what you expected.');
    }

    #[Test]
    public function it_ignores_steps_of_other_sessions(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();
        $step = SessionStep::factory()->for(Session::factory()->for($tester, 'tester'))->create();

        $this
            ->actingAs($tester)
            ->put(route('uat.sessions.steps.update', [$session, $step]), ['verdict' => 'accepted'])
            ->assertNotFound();
    }
}
