<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Boeq\Uat\Concerns\TestsAllScenarios;
use Boeq\Uat\Contracts\TestsFeatures;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements TestsFeatures
{
    use TestsAllScenarios;

    protected $guarded = [];

    public function canTestFeatures(): bool
    {
        return true;
    }
}
