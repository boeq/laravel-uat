<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Feedback;

use Boeq\Uat\Contracts\Feedback\ResolvesFeedback;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Events\FeedbackResolvedEvent;
use Boeq\Uat\Models\SessionStep;

class ResolveFeedback implements ResolvesFeedback
{
    public function resolve(SessionStep $step, ?string $note): void
    {
        $step->update([
            'feedback_status' => FeedbackStatus::Resolved,
            'resolution_note' => $note,
            'resolved_at' => now(),
        ]);

        FeedbackResolvedEvent::dispatch($step);
    }

    public static function bind(): void
    {
        app()->singleton(ResolvesFeedback::class, static::class);
    }
}
