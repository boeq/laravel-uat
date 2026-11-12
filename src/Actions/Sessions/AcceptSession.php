<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Sessions;

use Boeq\Uat\Contracts\Sessions\AcceptsSession;
use Boeq\Uat\Contracts\Sessions\CompletesSession;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;

class AcceptSession implements AcceptsSession
{
    public function __construct(
        protected CompletesSession $completesSession,
    ) {}

    public function accept(Session $session): void
    {
        SessionStep::query()
            ->whereBelongsTo($session)
            ->whereNull('verdict')
            ->update([
                'verdict' => Verdict::Accepted,
                'app_locale' => app()->getLocale(),
                'judged_at' => now(),
            ]);

        $this->completesSession->complete($session, null);
    }

    public static function bind(): void
    {
        app()->singleton(AcceptsSession::class, static::class);
    }
}
