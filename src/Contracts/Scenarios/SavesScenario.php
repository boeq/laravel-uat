<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Scenarios;

use Boeq\Uat\Data\ScenarioData;
use Boeq\Uat\Models\Scenario;

interface SavesScenario
{
    public function save(Scenario $scenario, ScenarioData $data): void;
}
