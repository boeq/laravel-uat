<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum Verdict: string
{
    case Accepted = 'accepted';
    case Feedback = 'feedback';
}
