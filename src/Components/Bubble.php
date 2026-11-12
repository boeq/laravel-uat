<?php

declare(strict_types=1);

namespace Boeq\Uat\Components;

use Boeq\Uat\Contracts\Access\ResolvesTester;
use Boeq\Uat\Contracts\Scenarios\ListsScenariosForTester;
use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Enums\Position;
use Boeq\Uat\Models\Session;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Bubble extends Component
{
    public function __construct(
        protected Guard $guard,
        protected ResolvesTester $tester,
        protected ListsScenariosForTester $scenarios,
    ) {}

    #[\Override]
    public function shouldRender(): bool
    {
        return $this->tester->resolve($this->guard->user()) instanceof TestsFeatures;
    }

    #[\Override]
    public function render(): View
    {
        /** @var TestsFeatures&Model $tester */
        $tester = $this->guard->user();

        $session = Session::query()->inProgressBy($tester)->first();

        return view('uat::components.bubble', [
            'position' => Position::from(config()->string('uat.bubble.position')),
            'screen' => $session instanceof Session
                ? view('uat::screens.session', ['session' => $session, 'openStep' => $session->currentStep()])
                : view('uat::screens.scenarios', ['scenarios' => $this->scenarios->list($tester)]),
        ]);
    }
}
