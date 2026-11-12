<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Scenarios;

use Boeq\Uat\Data\TranslationData;
use Boeq\Uat\Models\Scenario;

interface SavesTranslation
{
    public function save(Scenario $scenario, TranslationData $data): void;
}
