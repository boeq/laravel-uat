<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Components;

use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class BubbleTest extends TestCase
{
    #[Test]
    public function it_renders_nothing_for_guests(): void
    {
        $this
            ->blade('<x-uat::bubble />')
            ->assertDontSee('uat-bubble');
    }

    #[Test]
    public function it_renders_the_scenarios(): void
    {
        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'title' => '::title::']);

        $this->actingAs(Tester::query()->create());

        $this
            ->blade('<x-uat::bubble />')
            ->assertSeeInOrder(['id="uat-bubble"', '<section', '::title::', 'Test', '1'], false);
    }

    #[Test]
    public function it_renders_the_session_in_progress(): void
    {
        $tester = Tester::query()->create();

        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id]);

        $session = Session::factory()->for($tester, 'tester')->for($scenario)->create();
        SessionStep::factory()->for($session)->create(['position' => 1, 'instruction' => '::instruction::']);
        SessionStep::factory()->for($session)->create(['position' => 2]);

        $this->actingAs($tester);

        $this
            ->blade('<x-uat::bubble />')
            ->assertSeeInOrder(['<section', '::instruction::', 'Step 1 of 2'], false);
    }
}
