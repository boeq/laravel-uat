<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Scenarios;

use Boeq\Uat\Contracts\Scenarios\SavesTranslation;
use Boeq\Uat\Data\TranslationData;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\StepTranslation;
use Illuminate\Support\Facades\DB;

class SaveTranslation implements SavesTranslation
{
    public function save(Scenario $scenario, TranslationData $data): void
    {
        DB::transaction(function () use ($scenario, $data): void {
            $scenario->translations()->updateOrCreate(['locale' => $data->locale], [
                'title' => $data->title,
                'description' => $data->description,
                'preconditions' => $data->preconditions,
                'start_url' => $data->start_url,
            ]);

            $data->collect('steps')->each(fn (array $step): StepTranslation => StepTranslation::query()->updateOrCreate(
                ['step_id' => $step['id'], 'locale' => $data->locale],
                [
                    'title' => $step['title'],
                    'instruction' => $step['instruction'],
                    'expected_result' => $step['expected_result'],
                    'link_label' => $step['link_label'] ?? null,
                    'url' => $step['url'] ?? null,
                ],
            ));
        });
    }

    public static function bind(): void
    {
        app()->singleton(SavesTranslation::class, static::class);
    }
}
