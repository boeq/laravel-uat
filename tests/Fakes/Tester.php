<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Fakes;

use Boeq\Uat\Concerns\TestsAllScenarios;
use Boeq\Uat\Contracts\TestsFeatures;
use Illuminate\Foundation\Auth\User;

class Tester extends User implements TestsFeatures
{
    use TestsAllScenarios;

    protected $table = 'testers';

    public function canTestFeatures(): bool
    {
        return true;
    }
}
