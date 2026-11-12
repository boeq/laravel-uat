<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Http\Requests\Api\FeedbackFilterRequest;
use Boeq\Uat\Models\SessionStep;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class FeedbackController
{
    public function __invoke(FeedbackFilterRequest $request): JsonResponse
    {
        $feedback = SessionStep::query()
            ->where('feedback_status', $request->enum('status', FeedbackStatus::class) ?? FeedbackStatus::Open)
            ->when(
                $request->integer('scenario'),
                fn (Builder $query, int $scenario): Builder => $query->whereRelation('session', 'scenario_id', $scenario),
            )
            ->with('session')
            ->latest('id')
            ->paginate();

        return response()->json(
            $feedback->through($this->feedback(...)),
        );
    }

    protected function feedback(SessionStep $step): array
    {
        return [
            'id' => $step->id,
            'scenario_id' => $step->session->scenario_id,
            'session_id' => $step->session_id,
            'tester' => [
                'type' => $step->session->tester_type,
                'id' => $step->session->tester_id,
            ],
            'locale' => $step->session->locale,
            'step' => $step->only(['position', 'title', 'instruction', 'expected_result', 'url']),
            'feedback' => $step->feedback,
            'status' => $step->feedback_status,
            'resolution_note' => $step->resolution_note,
            'resolved_at' => $step->resolved_at,
            'context' => $step->only([
                'page_url',
                'user_agent',
                'viewport_width',
                'viewport_height',
                'app_locale',
                'judged_at',
            ]),
        ];
    }
}
