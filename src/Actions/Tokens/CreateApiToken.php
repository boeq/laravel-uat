<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Tokens;

use Boeq\Uat\Contracts\Tokens\CreatesApiToken;
use Boeq\Uat\Models\ApiToken;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateApiToken implements CreatesApiToken
{
    public function create(string $name, Collection $abilities, ?CarbonInterface $expiresAt): string
    {
        $token = Str::random(40);

        ApiToken::query()->create([
            'name' => $name,
            'token' => hash('sha256', $token),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    public static function bind(): void
    {
        app()->singleton(CreatesApiToken::class, static::class);
    }
}
