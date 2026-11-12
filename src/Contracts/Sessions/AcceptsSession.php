<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Sessions;

use Boeq\Uat\Models\Session;

interface AcceptsSession
{
    public function accept(Session $session): void;
}
