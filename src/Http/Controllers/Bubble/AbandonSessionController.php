<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Session;
use Illuminate\Http\RedirectResponse;

class AbandonSessionController
{
    public function __invoke(Session $session): RedirectResponse
    {
        $session->update(['status' => SessionStatus::Abandoned]);

        return redirect()->route('uat.scenarios.index');
    }
}
