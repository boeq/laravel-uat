<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Sessions;

use Boeq\Uat\Data\VerdictData;
use Boeq\Uat\Models\SessionStep;

interface JudgesStep
{
    public function judge(SessionStep $step, VerdictData $data): void;
}
