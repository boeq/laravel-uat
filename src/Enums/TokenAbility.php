<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum TokenAbility: string
{
    case Read = 'read';
    case Write = 'write';
}
