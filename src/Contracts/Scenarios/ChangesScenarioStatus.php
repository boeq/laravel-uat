<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Scenarios;

use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Illuminate\Support\Collection;

interface ChangesScenarioStatus
{
    /** @return Collection<int, string> */
    public function change(Scenario $scenario, ScenarioStatus $status): Collection;
}
