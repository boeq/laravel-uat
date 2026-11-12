<?php

declare(strict_types=1);

namespace Boeq\Uat\Components;

use Boeq\Uat\Contracts\Scenarios\ListsScenariosForTester;
use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Data\ScenarioProgressData;
use Boeq\Uat\Models\Session;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Frame extends Component
{
    public ?Session $session;

    public int $unfinished;

    public function __construct(Guard $guard, ListsScenariosForTester $scenarios)
    {
        /** @var TestsFeatures&Model $tester */
        $tester = $guard->user();

        $this->session = Session::query()->inProgressBy($tester)->first();
        $this->unfinished = $scenarios->list($tester)
            ->reject(fn (ScenarioProgressData $progress): bool => $progress->isFinished())
            ->count();
    }

    #[\Override]
    public function render(): View
    {
        return view('uat::components.frame');
    }
}
