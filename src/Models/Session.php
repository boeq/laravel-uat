<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Database\Factories\SessionFactory;
use Boeq\Uat\Enums\SessionResult;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Enums\Verdict;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $scenario_id
 * @property string $tester_type
 * @property int|string $tester_id
 * @property string $locale
 * @property SessionStatus $status
 * @property ?string $closing_comment
 * @property ?Carbon $completed_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Scenario $scenario
 * @property-read BaseModel $tester
 * @property-read Collection<int, SessionStep> $steps
 */
#[UseFactory(SessionFactory::class)]
class Session extends Model
{
    /** @use HasFactory<SessionFactory> */
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return [
            'status' => SessionStatus::class,
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Scenario, $this> */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /** @return MorphTo<BaseModel, $this> */
    public function tester(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return HasMany<SessionStep, $this> */
    public function steps(): HasMany
    {
        return $this->hasMany(SessionStep::class)->orderBy('position');
    }

    public function currentStep(): ?SessionStep
    {
        return $this->steps->firstWhere('verdict', null);
    }

    public function result(): SessionResult
    {
        return $this->steps->contains('verdict', Verdict::Feedback)
            ? SessionResult::AcceptedWithFeedback
            : SessionResult::Accepted;
    }

    /** @param Builder<$this> $query */
    #[Scope]
    protected function inProgressBy(Builder $query, BaseModel $tester): void
    {
        $query
            ->whereMorphedTo('tester', $tester)
            ->where('status', SessionStatus::InProgress);
    }

    /** @param Builder<$this> $query */
    #[Scope]
    protected function unfinished(Builder $query): void
    {
        $query->whereIn('status', [SessionStatus::InProgress, SessionStatus::Paused]);
    }
}
