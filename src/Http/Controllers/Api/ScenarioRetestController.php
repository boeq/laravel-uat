<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Models\Scenario;
use Illuminate\Http\JsonResponse;

class ScenarioRetestController
{
    public function __invoke(Scenario $scenario): JsonResponse
    {
        $scenario->update(['ready_for_retest_at' => now()]);

        return response()->json([
            'data' => [
                'id' => $scenario->id,
                'ready_for_retest_at' => $scenario->ready_for_retest_at,
            ],
        ]);
    }
}
