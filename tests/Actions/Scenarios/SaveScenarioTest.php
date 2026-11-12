<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Scenarios;

use Boeq\Uat\Actions\Scenarios\SaveScenario;
use Boeq\Uat\Data\ScenarioData;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Models\StepTranslation;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SaveScenarioTest extends TestCase
{
    #[Test]
    public function it_creates_a_scenario(): void
    {
        $scenario = new Scenario(['status' => ScenarioStatus::Draft]);

        $action = app(SaveScenario::class);
        $action->save($scenario, new ScenarioData([
            'start_url' => '/orders',
            'translations' => [
                'en' => ['title' => '::title::', 'description' => '::description::'],
            ],
            'steps' => [
                [
                    'url' => '/orders/create',
                    'translations' => [
                        'en' => ['title' => '::first::', 'instruction' => '::instruction::', 'expected_result' => '::result::'],
                    ],
                ],
                [
                    'translations' => [
                        'en' => ['title' => '::second::', 'instruction' => '::instruction::', 'expected_result' => '::result::'],
                    ],
                ],
            ],
        ]));

        $this->assertSame('/orders', $scenario->refresh()->start_url);
        $this->assertSame('::title::', $scenario->translation('en')->title);
        $this->assertSame(
            [[1, '/orders/create', '::first::'], [2, null, '::second::']],
            $scenario->steps->map(fn (Step $step): array => [$step->position, $step->url, $step->translation('en')->title])->all(),
        );
    }

    #[Test]
    public function it_updates_a_scenario(): void
    {
        $scenario = Scenario::factory()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'locale' => 'nl']);

        $removed = Step::factory()->create(['scenario_id' => $scenario->id, 'position' => 1]);
        $kept = Step::factory()->create(['scenario_id' => $scenario->id, 'position' => 2]);
        StepTranslation::factory()->create(['step_id' => $kept->id, 'locale' => 'nl']);

        $action = app(SaveScenario::class);
        $action->save($scenario, new ScenarioData([
            'translations' => [
                'en' => ['title' => '::title::', 'description' => '::description::'],
            ],
            'steps' => [
                [
                    'id' => $kept->id,
                    'translations' => [
                        'en' => ['title' => '::kept::', 'instruction' => '::instruction::', 'expected_result' => '::result::'],
                    ],
                ],
                [
                    'translations' => [
                        'en' => ['title' => '::added::', 'instruction' => '::instruction::', 'expected_result' => '::result::'],
                    ],
                ],
            ],
        ]));

        $this->assertModelMissing($removed);
        $this->assertSame(['en'], $scenario->translations->pluck('locale')->all());
        $this->assertSame(1, $kept->refresh()->position);
        $this->assertSame(['en'], $kept->translations->pluck('locale')->all());
        $this->assertSame(
            [[1, '::kept::'], [2, '::added::']],
            $scenario->steps->map(fn (Step $step): array => [$step->position, $step->translation('en')->title])->all(),
        );
    }
}
