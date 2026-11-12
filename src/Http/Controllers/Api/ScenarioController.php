<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Contracts\Scenarios\SavesScenario;
use Boeq\Uat\Data\ScenarioData;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Http\Requests\Api\ScenarioFilterRequest;
use Boeq\Uat\Http\Requests\Api\ScenarioRequest;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Models\StepTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ScenarioController
{
    public function index(ScenarioFilterRequest $request): JsonResponse
    {
        $scenarios = Scenario::query()
            ->with(['translations', 'steps.translations'])
            ->withCount($this->resultCounts())
            ->when(
                $request->enum('status', ScenarioStatus::class),
                fn (Builder $query, ScenarioStatus $status): Builder => $query->where('status', $status),
            )
            ->get();

        return response()->json([
            'data' => $scenarios->map($this->scenario(...)),
        ]);
    }

    public function store(ScenarioRequest $request, SavesScenario $action): JsonResponse
    {
        $scenario = new Scenario(['status' => ScenarioStatus::Draft]);

        $action->save($scenario, new ScenarioData($request->validated()));

        return $this->show($scenario)->setStatusCode(201);
    }

    public function show(Scenario $scenario): JsonResponse
    {
        return response()->json([
            'data' => $this->scenario($scenario->loadCount($this->resultCounts())),
        ]);
    }

    public function update(ScenarioRequest $request, Scenario $scenario, SavesScenario $action): JsonResponse
    {
        $action->save($scenario, new ScenarioData($request->validated()));

        return $this->show($scenario);
    }

    public function destroy(Scenario $scenario): JsonResponse
    {
        abort_if($scenario->sessions()->exists(), 409, 'A scenario with sessions can only be archived.');

        $scenario->delete();

        return response()->json(status: 204);
    }

    protected function scenario(Scenario $scenario): array
    {
        return [
            'id' => $scenario->id,
            'status' => $scenario->status,
            'start_url' => $scenario->start_url,
            'ready_for_retest_at' => $scenario->ready_for_retest_at,
            'translations' => $scenario->translations->mapWithKeys(fn (ScenarioTranslation $translation): array => [
                $translation->locale => $translation->only(['title', 'description', 'preconditions', 'start_url']),
            ]),
            'steps' => $scenario->steps->map($this->step(...)),
            'results' => $scenario->only([
                'sessions_count',
                'completed_sessions_count',
                'accepted_steps_count',
                'feedback_steps_count',
                'open_feedback_count',
            ]),
            'created_at' => $scenario->created_at,
            'updated_at' => $scenario->updated_at,
        ];
    }

    protected function resultCounts(): array
    {
        return [
            'sessions',
            'sessions as completed_sessions_count' => fn (Builder $query): Builder => $query->where('status', SessionStatus::Completed),
            'sessionSteps as accepted_steps_count' => fn (Builder $query): Builder => $query->where('verdict', Verdict::Accepted),
            'sessionSteps as feedback_steps_count' => fn (Builder $query): Builder => $query->where('verdict', Verdict::Feedback),
            'sessionSteps as open_feedback_count' => fn (Builder $query): Builder => $query->where('feedback_status', FeedbackStatus::Open),
        ];
    }

    protected function step(Step $step): array
    {
        return [
            'id' => $step->id,
            'position' => $step->position,
            'url' => $step->url,
            'translations' => $step->translations->mapWithKeys(fn (StepTranslation $translation): array => [
                $translation->locale => $translation->only(['title', 'instruction', 'expected_result', 'link_label', 'url']),
            ]),
        ];
    }
}
