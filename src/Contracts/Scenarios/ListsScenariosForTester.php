<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Scenarios;

use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Data\ScenarioProgressData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ListsScenariosForTester
{
    /** @return Collection<int, ScenarioProgressData> */
    public function list(TestsFeatures&Model $tester): Collection;
}
