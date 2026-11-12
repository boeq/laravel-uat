<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Tokens;

use Boeq\Uat\Enums\TokenAbility;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

interface CreatesApiToken
{
    /** @param Collection<int, TokenAbility> $abilities */
    public function create(string $name, Collection $abilities, ?CarbonInterface $expiresAt): string;
}
