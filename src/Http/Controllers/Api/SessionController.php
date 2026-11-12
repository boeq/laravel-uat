<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Illuminate\Http\JsonResponse;

class SessionController
{
    public function index(Scenario $scenario): JsonResponse
    {
        $sessions = $scenario->sessions()
            ->with('steps')
            ->latest('id')
            ->paginate();

        return response()->json(
            $sessions->through($this->session(...)),
        );
    }

    public function show(Session $session): JsonResponse
    {
        return response()->json([
            'data' => $this->session($session),
        ]);
    }

    protected function session(Session $session): array
    {
        return [
            'id' => $session->id,
            'scenario_id' => $session->scenario_id,
            'tester' => [
                'type' => $session->tester_type,
                'id' => $session->tester_id,
            ],
            'locale' => $session->locale,
            'status' => $session->status,
            'result' => $session->status === SessionStatus::Completed ? $session->result() : null,
            'closing_comment' => $session->closing_comment,
            'started_at' => $session->created_at,
            'completed_at' => $session->completed_at,
            'steps' => $session->steps->map($this->step(...)),
        ];
    }

    protected function step(SessionStep $step): array
    {
        return [
            ...$step->only([
                'id',
                'step_id',
                'position',
                'title',
                'instruction',
                'expected_result',
                'link_label',
                'url',
                'verdict',
                'feedback',
                'feedback_status',
                'resolution_note',
                'resolved_at',
            ]),
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
