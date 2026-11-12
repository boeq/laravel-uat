<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Concerns\HasTranslations;
use Boeq\Uat\Database\Factories\StepFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $scenario_id
 * @property int $position
 * @property ?string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Collection<int, StepTranslation> $translations
 */
#[UseFactory(StepFactory::class)]
class Step extends Model
{
    /** @use HasFactory<StepFactory> */
    use HasFactory;

    /** @use HasTranslations<StepTranslation> */
    use HasTranslations;

    /** @return HasMany<StepTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(StepTranslation::class);
    }
}
