<?php

declare(strict_types=1);

namespace Boeq\Uat\Enums;

enum ScenarioStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
