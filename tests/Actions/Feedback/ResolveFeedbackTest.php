<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Feedback;

use Boeq\Uat\Actions\Feedback\ResolveFeedback;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Events\FeedbackResolvedEvent;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;

final class ResolveFeedbackTest extends TestCase
{
    #[Test]
    public function it_resolves_feedback(): void
    {
        Event::fake();
        $this->freezeSecond();

        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();
        $step = SessionStep::factory()->for($session)->create([
            'verdict' => Verdict::Feedback,
            'feedback_status' => FeedbackStatus::Open,
        ]);

        $action = app(ResolveFeedback::class);
        $action->resolve($step, '::note::');

        $step->refresh();

        $this->assertSame(FeedbackStatus::Resolved, $step->feedback_status);
        $this->assertSame('::note::', $step->resolution_note);
        $this->assertSame(now()->toDateTimeString(), $step->resolved_at?->toDateTimeString());

        Event::assertDispatched(FeedbackResolvedEvent::class, fn (FeedbackResolvedEvent $event): bool => $event->step->is($step));
    }
}
