<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class ConfigurationController
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'locales' => config()->array('uat.locales'),
                'default_locale' => config()->string('uat.default_locale'),
            ],
        ]);
    }
}
