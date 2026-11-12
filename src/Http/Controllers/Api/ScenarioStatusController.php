<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Contracts\Scenarios\ChangesScenarioStatus;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Http\Requests\Api\StatusRequest;
use Boeq\Uat\Models\Scenario;
use Illuminate\Http\JsonResponse;

class ScenarioStatusController
{
    public function __invoke(StatusRequest $request, Scenario $scenario, ChangesScenarioStatus $action): JsonResponse
    {
        /** @var ScenarioStatus $status */
        $status = $request->enum('status', ScenarioStatus::class);

        $warnings = $action->change($scenario, $status);

        return response()->json([
            'data' => [
                'id' => $scenario->id,
                'status' => $scenario->status,
            ],
            'warnings' => $warnings,
        ]);
    }
}
