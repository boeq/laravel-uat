<?php

declare(strict_types=1);

namespace Boeq\Uat\Data;

use Illuminate\Support\Collection;

/**
 * @property ?string $start_url
 * @property array<string, array<string, ?string>> $translations
 * @property array<int, array<string, mixed>> $steps
 *
 * @extends Data<string, mixed>
 */
class ScenarioData extends Data
{
    /** @return Collection<int, StepData> */
    public function steps(): Collection
    {
        return $this->collect('steps')->mapInto(StepData::class);
    }
}
