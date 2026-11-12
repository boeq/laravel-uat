<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Sessions;

use Boeq\Uat\Models\Session;

interface CompletesSession
{
    public function complete(Session $session, ?string $closingComment): void;
}
