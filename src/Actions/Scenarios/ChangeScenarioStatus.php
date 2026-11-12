<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Scenarios;

use Boeq\Uat\Contracts\Scenarios\ChangesScenarioStatus;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ChangeScenarioStatus implements ChangesScenarioStatus
{
    public function change(Scenario $scenario, ScenarioStatus $status): Collection
    {
        if ($status !== ScenarioStatus::Active) {
            $scenario->update(['status' => $status]);

            return collect();
        }

        $defaultLocale = config()->string('uat.default_locale');

        $missing = $this->missingTranslations($scenario, $defaultLocale);

        if ($scenario->steps->isEmpty()) {
            $missing->push('The scenario has no steps.');
        }

        if ($missing->isNotEmpty()) {
            throw ValidationException::withMessages(['status' => $missing->all()]);
        }

        $scenario->update(['status' => $status]);

        return collect(config()->array('uat.locales'))
            ->reject(fn (string $locale): bool => $locale === $defaultLocale)
            ->flatMap(fn (string $locale): Collection => $this->missingTranslations($scenario, $locale))
            ->values();
    }

    public static function bind(): void
    {
        app()->singleton(ChangesScenarioStatus::class, static::class);
    }

    /** @return Collection<int, string> */
    protected function missingTranslations(Scenario $scenario, string $locale): Collection
    {
        /** @var Collection<int, string> $messages */
        $messages = $scenario->steps
            ->reject(fn (Step $step): bool => $step->translations->contains('locale', $locale))
            ->map(fn (Step $step): string => 'Step '.$step->position.' has no '.$locale.' translation.')
            ->values()
            ->toBase();

        if (! $scenario->translations->contains('locale', $locale)) {
            $messages->prepend('The scenario has no '.$locale.' translation.');
        }

        return $messages;
    }
}
