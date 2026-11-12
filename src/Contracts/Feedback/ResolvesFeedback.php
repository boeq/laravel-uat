<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Feedback;

use Boeq\Uat\Models\SessionStep;

interface ResolvesFeedback
{
    public function resolve(SessionStep $step, ?string $note): void;
}
