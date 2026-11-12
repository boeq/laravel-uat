<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Scenarios;

use Boeq\Uat\Actions\Scenarios\ChangeScenarioStatus;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Models\StepTranslation;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

final class ChangeScenarioStatusTest extends TestCase
{
    #[Test]
    public function it_archives_a_scenario(): void
    {
        $scenario = Scenario::factory()->active()->create();

        $action = app(ChangeScenarioStatus::class);
        $warnings = $action->change($scenario, ScenarioStatus::Archived);

        $this->assertSame(ScenarioStatus::Archived, $scenario->refresh()->status);
        $this->assertSame([], $warnings->all());
    }

    #[Test]
    public function it_activates_a_scenario(): void
    {
        config()->set('uat.locales', ['en', 'nl']);

        $scenario = Scenario::factory()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'locale' => 'en']);

        $step = Step::factory()->create(['scenario_id' => $scenario->id]);
        StepTranslation::factory()->create(['step_id' => $step->id, 'locale' => 'en']);

        $action = app(ChangeScenarioStatus::class);
        $warnings = $action->change($scenario, ScenarioStatus::Active);

        $this->assertSame(ScenarioStatus::Active, $scenario->refresh()->status);
        $this->assertSame(
            ['The scenario has no nl translation.', 'Step 1 has no nl translation.'],
            $warnings->all(),
        );
    }

    #[Test]
    public function it_rejects_an_incomplete_scenario(): void
    {
        $scenario = Scenario::factory()->create();

        $action = app(ChangeScenarioStatus::class);

        $this->assertThrows(
            fn () => $action->change($scenario, ScenarioStatus::Active),
            fn (Throwable $exception): bool => $exception instanceof ValidationException && $exception->errors() === [
                'status' => ['The scenario has no en translation.', 'The scenario has no steps.'],
            ],
        );

        $this->assertSame(ScenarioStatus::Draft, $scenario->refresh()->status);
    }
}
