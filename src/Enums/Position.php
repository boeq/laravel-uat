<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum Position: string
{
    case BottomRight = 'bottom-right';
    case BottomLeft = 'bottom-left';
    case TopRight = 'top-right';
    case TopLeft = 'top-left';
}
