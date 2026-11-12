<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Models\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BubbleController
{
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Model $tester */
        $tester = $request->user();

        $session = Session::query()->inProgressBy($tester)->first();

        return $session instanceof Session
            ? redirect()->route('uat.sessions.show', $session)
            : redirect()->route('uat.scenarios.index');
    }
}
