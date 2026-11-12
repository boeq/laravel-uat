<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Database\Factories\SessionStepFactory;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\Verdict;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $session_id
 * @property ?int $step_id
 * @property int $position
 * @property string $title
 * @property string $instruction
 * @property string $expected_result
 * @property ?string $link_label
 * @property ?string $url
 * @property ?Verdict $verdict
 * @property ?string $feedback
 * @property ?FeedbackStatus $feedback_status
 * @property ?string $resolution_note
 * @property ?Carbon $resolved_at
 * @property ?string $page_url
 * @property ?string $user_agent
 * @property ?int $viewport_width
 * @property ?int $viewport_height
 * @property ?string $app_locale
 * @property ?Carbon $judged_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Session $session
 */
#[UseFactory(SessionStepFactory::class)]
class SessionStep extends Model
{
    /** @use HasFactory<SessionStepFactory> */
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return [
            'verdict' => Verdict::class,
            'feedback_status' => FeedbackStatus::class,
            'resolved_at' => 'datetime',
            'judged_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Session, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
