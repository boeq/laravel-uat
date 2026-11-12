<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Database\Factories\ApiTokenFactory;
use Boeq\Uat\Enums\TokenAbility;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $token
 * @property Collection<int, TokenAbility> $abilities
 * @property ?Carbon $expires_at
 * @property ?Carbon $last_used_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[UseFactory(ApiTokenFactory::class)]
class ApiToken extends Model
{
    /** @use HasFactory<ApiTokenFactory> */
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return [
            'abilities' => AsEnumCollection::of(TokenAbility::class),
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }
}
