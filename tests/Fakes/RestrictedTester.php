<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Fakes;

use Boeq\Uat\Models\Scenario;

class RestrictedTester extends Tester
{
    #[\Override]
    public function canTestScenario(Scenario $scenario): bool
    {
        return false;
    }
}
