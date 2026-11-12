<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Scenarios;

use Boeq\Uat\Contracts\Scenarios\ListsScenariosForTester;
use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Data\ScenarioProgressData;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ListScenariosForTester implements ListsScenariosForTester
{
    public function list(TestsFeatures&Model $tester): Collection
    {
        return Scenario::query()
            ->where('status', ScenarioStatus::Active)
            ->with([
                'translations',
                'sessions' => fn (HasMany $sessions): HasMany => $sessions
                    ->whereMorphedTo('tester', $tester)
                    ->where('status', '!=', SessionStatus::Abandoned)
                    ->with('steps')
                    ->latest('id'),
            ])
            ->withCount('steps')
            ->orderBy('id')
            ->get()
            ->filter(fn (Scenario $scenario): bool => $tester->canTestScenario($scenario))
            ->map(fn (Scenario $scenario): ScenarioProgressData => new ScenarioProgressData([
                'scenario' => $scenario,
                'session' => $scenario->sessions->first(),
            ]))
            ->values()
            ->toBase();
    }

    public static function bind(): void
    {
        app()->singleton(ListsScenariosForTester::class, static::class);
    }
}
