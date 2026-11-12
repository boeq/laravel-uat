<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Sessions;

use Boeq\Uat\Actions\Sessions\CompleteSession;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Events\SessionCompletedEvent;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;

final class CompleteSessionTest extends TestCase
{
    #[Test]
    public function it_completes_a_session(): void
    {
        Event::fake();
        $this->freezeSecond();

        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        $action = app(CompleteSession::class);
        $action->complete($session, '::closing-comment::');

        $session->refresh();

        $this->assertSame(SessionStatus::Completed, $session->status);
        $this->assertSame('::closing-comment::', $session->closing_comment);
        $this->assertSame(now()->toDateTimeString(), $session->completed_at?->toDateTimeString());

        Event::assertDispatched(SessionCompletedEvent::class, fn (SessionCompletedEvent $event): bool => $event->session->is($session));
    }
}
