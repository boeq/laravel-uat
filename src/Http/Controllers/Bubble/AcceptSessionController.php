<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\AcceptsSession;
use Boeq\Uat\Models\Session;
use Illuminate\Http\RedirectResponse;

class AcceptSessionController
{
    public function __invoke(Session $session, AcceptsSession $action): RedirectResponse
    {
        $action->accept($session);

        return redirect()->route('uat.scenarios.index');
    }
}
