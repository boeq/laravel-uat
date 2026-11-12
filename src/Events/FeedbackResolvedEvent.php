<?php

declare(strict_types=1);

namespace Boeq\Uat\Events;

use Boeq\Uat\Models\SessionStep;
use Illuminate\Foundation\Events\Dispatchable;

class FeedbackResolvedEvent
{
    use Dispatchable;

    public function __construct(
        public SessionStep $step,
    ) {}
}
