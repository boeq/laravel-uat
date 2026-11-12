<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\JudgesStep;
use Boeq\Uat\Data\VerdictData;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Http\Requests\Bubble\VerdictRequest;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Illuminate\Http\RedirectResponse;

class SessionStepController
{
    public function __invoke(VerdictRequest $request, Session $session, SessionStep $step, JudgesStep $action): RedirectResponse
    {
        $action->judge($step, new VerdictData([
            'verdict' => $request->enum('verdict', Verdict::class),
            'feedback' => $request->validated('feedback'),
            'page_url' => $request->validated('page_url'),
            'viewport_width' => $request->integer('viewport_width'),
            'viewport_height' => $request->integer('viewport_height'),
            'user_agent' => $request->userAgent(),
        ]));

        return redirect()->route('uat.sessions.show', $session);
    }
}
