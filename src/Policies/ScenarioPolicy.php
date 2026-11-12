<?php

declare(strict_types=1);

namespace Boeq\Uat\Policies;

use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;

class ScenarioPolicy
{
    public function test(TestsFeatures $tester, Scenario $scenario): bool
    {
        return $scenario->status === ScenarioStatus::Active
            && $tester->canTestScenario($scenario);
    }
}
