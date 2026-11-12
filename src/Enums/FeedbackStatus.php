<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum FeedbackStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
}
