<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\CompletesSession;
use Boeq\Uat\Http\Requests\Bubble\CompleteSessionRequest;
use Boeq\Uat\Models\Session;
use Illuminate\Http\RedirectResponse;

class CompleteSessionController
{
    public function __invoke(CompleteSessionRequest $request, Session $session, CompletesSession $action): RedirectResponse
    {
        /** @var ?string $closingComment */
        $closingComment = $request->validated('closing_comment');

        $action->complete($session, $closingComment);

        return redirect()->route('uat.scenarios.index');
    }
}
