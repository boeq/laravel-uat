<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Boeq\Uat\Contracts\Scenarios\SavesTranslation;
use Boeq\Uat\Data\TranslationData;
use Boeq\Uat\Http\Requests\Api\TranslationRequest;
use Boeq\Uat\Models\Scenario;
use Illuminate\Http\JsonResponse;

class ScenarioTranslationController
{
    public function __invoke(TranslationRequest $request, Scenario $scenario, SavesTranslation $action): JsonResponse
    {
        $action->save($scenario, new TranslationData($request->validated()));

        return response()->json(status: 204);
    }
}
