<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\StartsSession;
use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SessionController
{
    public function store(Request $request, Scenario $scenario, StartsSession $action): RedirectResponse
    {
        /** @var TestsFeatures&Model $tester */
        $tester = $request->user();

        $session = $action->start($tester, $scenario);

        return redirect()->route('uat.sessions.show', $session);
    }

    public function show(Request $request, Session $session): View
    {
        return view('uat::screens.session', [
            'session' => $session,
            'openStep' => $session->steps->find($request->integer('step')) ?? $session->currentStep(),
        ]);
    }
}
