<?php

declare(strict_types=1);

namespace Boeq\Uat\Events;

use Boeq\Uat\Models\Session;
use Illuminate\Foundation\Events\Dispatchable;

class SessionStartedEvent
{
    use Dispatchable;

    public function __construct(
        public Session $session,
    ) {}
}
