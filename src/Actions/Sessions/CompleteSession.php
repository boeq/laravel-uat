<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Sessions;

use Boeq\Uat\Contracts\Sessions\CompletesSession;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Events\SessionCompletedEvent;
use Boeq\Uat\Models\Session;

class CompleteSession implements CompletesSession
{
    public function complete(Session $session, ?string $closingComment): void
    {
        $session->update([
            'status' => SessionStatus::Completed,
            'closing_comment' => $closingComment,
            'completed_at' => now(),
        ]);

        SessionCompletedEvent::dispatch($session);
    }

    public static function bind(): void
    {
        app()->singleton(CompletesSession::class, static::class);
    }
}
