<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Fakes;

class DeniedTester extends Tester
{
    #[\Override]
    public function canTestFeatures(): bool
    {
        return false;
    }
}
