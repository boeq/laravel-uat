<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Scenarios\ListsScenariosForTester;
use Boeq\Uat\Contracts\TestsFeatures;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ScenarioController
{
    public function __invoke(Request $request, ListsScenariosForTester $action): View
    {
        /** @var TestsFeatures&Model $tester */
        $tester = $request->user();

        return view('uat::screens.scenarios', [
            'scenarios' => $action->list($tester),
        ]);
    }
}
