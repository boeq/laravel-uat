<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum SessionResult: string
{
    case Accepted = 'accepted';
    case AcceptedWithFeedback = 'accepted_with_feedback';
}
