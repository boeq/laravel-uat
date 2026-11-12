<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioControllerTest extends TestCase
{
    #[Test]
    public function it_lists_the_scenarios(): void
    {
        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'title' => '::title::']);
        Step::factory()->count(2)->create(['scenario_id' => $scenario->id]);

        $draft = Scenario::factory()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $draft->id, 'title' => '::draft::']);

        $this
            ->actingAs(Tester::query()->create())
            ->get(route('uat.scenarios.index'))
            ->assertOk()
            ->assertSeeInOrder([route('uat.sessions.store', $scenario), '::title::', '2 steps', 'Not started'])
            ->assertDontSee('::draft::');
    }

    #[Test]
    public function it_moves_completed_scenarios_below_the_list(): void
    {
        $tester = Tester::query()->create();

        $completed = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $completed->id, 'title' => '::completed::']);
        Session::factory()->for($tester, 'tester')->for($completed)->create([
            'status' => SessionStatus::Completed,
            'completed_at' => now(),
        ]);

        $open = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $open->id, 'title' => '::open::']);

        $this
            ->actingAs($tester)
            ->get(route('uat.scenarios.index'))
            ->assertOk()
            ->assertSeeInOrder(['::open::', '<details>', 'Completed tests (1)', '::completed::'], false);
    }

    #[Test]
    public function it_has_no_link_without_completed_scenarios(): void
    {
        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id]);

        $this
            ->actingAs(Tester::query()->create())
            ->get(route('uat.scenarios.index'))
            ->assertOk()
            ->assertDontSee('Completed tests');
    }
}
