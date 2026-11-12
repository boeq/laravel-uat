<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Sessions;

use Boeq\Uat\Contracts\Sessions\JudgesStep;
use Boeq\Uat\Data\VerdictData;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Events\FeedbackSubmittedEvent;
use Boeq\Uat\Models\SessionStep;

class JudgeStep implements JudgesStep
{
    public function judge(SessionStep $step, VerdictData $data): void
    {
        $hasFeedback = $data->verdict === Verdict::Feedback;

        $step->update([
            'verdict' => $data->verdict,
            'feedback' => $hasFeedback ? $data->feedback : null,
            'feedback_status' => $hasFeedback ? FeedbackStatus::Open : null,
            'page_url' => $data->page_url,
            'user_agent' => $data->user_agent,
            'viewport_width' => $data->viewport_width,
            'viewport_height' => $data->viewport_height,
            'app_locale' => app()->getLocale(),
            'judged_at' => now(),
        ]);

        if ($hasFeedback) {
            FeedbackSubmittedEvent::dispatch($step);
        }
    }

    public static function bind(): void
    {
        app()->singleton(JudgesStep::class, static::class);
    }
}
