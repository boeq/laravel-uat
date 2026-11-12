<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Sessions;

use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Illuminate\Database\Eloquent\Model;

interface StartsSession
{
    public function start(TestsFeatures&Model $tester, Scenario $scenario): Session;
}
