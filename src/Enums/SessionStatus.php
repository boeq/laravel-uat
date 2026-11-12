<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum SessionStatus: string
{
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Completed = 'completed';
    case Abandoned = 'abandoned';

    public function isUnfinished(): bool
    {
        return $this === self::InProgress || $this === self::Paused;
    }
}
