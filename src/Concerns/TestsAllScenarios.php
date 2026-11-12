<?php

declare(strict_types=1);

namespace Boeq\Uat\Concerns;

use Boeq\Uat\Models\Scenario;

trait TestsAllScenarios
{
    public function canTestScenario(Scenario $scenario): bool
    {
        return true;
    }
}
