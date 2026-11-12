<?php

declare(strict_types=1);

namespace Boeq\Uat\Data;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Illuminate\Support\Carbon;

/**
 * @property Scenario $scenario
 * @property ?Session $session
 *
 * @extends Data<string, mixed>
 */
class ScenarioProgressData extends Data
{
    public function needsRetest(): bool
    {
        $completedAt = $this->session?->completed_at;

        return $completedAt instanceof Carbon
            && $this->scenario->ready_for_retest_at?->isAfter($completedAt) === true;
    }

    public function isFinished(): bool
    {
        return $this->session?->status === SessionStatus::Completed
            && ! $this->needsRetest();
    }
}
