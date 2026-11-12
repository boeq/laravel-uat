<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Concerns\HasTranslations;
use Boeq\Uat\Database\Factories\ScenarioFactory;
use Boeq\Uat\Enums\ScenarioStatus;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ScenarioStatus $status
 * @property ?string $start_url
 * @property ?Carbon $ready_for_retest_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Collection<int, ScenarioTranslation> $translations
 * @property-read Collection<int, Step> $steps
 * @property-read Collection<int, Session> $sessions
 */
#[UseFactory(ScenarioFactory::class)]
class Scenario extends Model
{
    /** @use HasFactory<ScenarioFactory> */
    use HasFactory;

    /** @use HasTranslations<ScenarioTranslation> */
    use HasTranslations;

    #[\Override]
    protected function casts(): array
    {
        return [
            'status' => ScenarioStatus::class,
            'ready_for_retest_at' => 'datetime',
        ];
    }

    /** @return HasMany<ScenarioTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(ScenarioTranslation::class);
    }

    /** @return HasMany<Step, $this> */
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class)->orderBy('position');
    }

    /** @return HasMany<Session, $this> */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /** @return HasManyThrough<SessionStep, Session, $this> */
    public function sessionSteps(): HasManyThrough
    {
        return $this->hasManyThrough(SessionStep::class, Session::class);
    }
}
