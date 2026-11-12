<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts;

use Boeq\Uat\Models\Scenario;

interface TestsFeatures
{
    public function canTestFeatures(): bool;

    public function canTestScenario(Scenario $scenario): bool;
}
