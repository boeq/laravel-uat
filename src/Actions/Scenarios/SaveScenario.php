<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Scenarios;

use Boeq\Uat\Contracts\Scenarios\SavesScenario;
use Boeq\Uat\Data\ScenarioData;
use Boeq\Uat\Data\StepData;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaveScenario implements SavesScenario
{
    public function save(Scenario $scenario, ScenarioData $data): void
    {
        DB::transaction(function () use ($scenario, $data): void {
            $scenario->start_url = $data->start_url;
            $scenario->save();

            $this->replaceTranslations($scenario, $data->collect('translations'));

            $steps = $data->steps()->map(
                fn (StepData $step, int $index): Step => $this->saveStep($scenario, $step, $index + 1),
            );

            $scenario->steps()->whereKeyNot($steps->pluck('id'))->delete();
        });
    }

    public static function bind(): void
    {
        app()->singleton(SavesScenario::class, static::class);
    }

    protected function saveStep(Scenario $scenario, StepData $data, int $position): Step
    {
        $step = $scenario->steps()->findOrNew($data->id);
        $step->position = $position;
        $step->url = $data->url;
        $step->save();

        $this->replaceTranslations($step, $data->collect('translations'));

        return $step;
    }

    /** @param Collection<string, array<string, ?string>> $translations */
    protected function replaceTranslations(Scenario|Step $model, Collection $translations): void
    {
        $model->translations()->delete();

        $model->translations()->createMany(
            $translations->map(fn (array $translation, string $locale): array => [...$translation, 'locale' => $locale]),
        );
    }
}
