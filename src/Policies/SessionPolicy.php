<?php

declare(strict_types=1);

namespace Boeq\Uat\Policies;

use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Illuminate\Database\Eloquent\Model;

class SessionPolicy
{
    public function update(Model $tester, Session $session): bool
    {
        return $session->tester()->is($tester)
            && $session->status->isUnfinished();
    }

    public function complete(Model $tester, Session $session): bool
    {
        return $this->update($tester, $session)
            && ! $session->currentStep() instanceof SessionStep;
    }
}
