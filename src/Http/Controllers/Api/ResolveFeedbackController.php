<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Contracts\Feedback\ResolvesFeedback;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Http\Requests\Api\ResolveFeedbackRequest;
use Boeq\Uat\Models\SessionStep;
use Illuminate\Http\JsonResponse;

class ResolveFeedbackController
{
    public function __invoke(ResolveFeedbackRequest $request, SessionStep $step, ResolvesFeedback $action): JsonResponse
    {
        abort_unless($step->verdict === Verdict::Feedback, 404);

        /** @var ?string $note */
        $note = $request->validated('note');

        $action->resolve($step, $note);

        return response()->json(status: 204);
    }
}
