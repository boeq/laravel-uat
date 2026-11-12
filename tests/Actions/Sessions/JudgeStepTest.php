<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Sessions;

use Boeq\Uat\Actions\Sessions\JudgeStep;
use Boeq\Uat\Data\VerdictData;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Events\FeedbackSubmittedEvent;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class JudgeStepTest extends TestCase
{
    #[Test]
    #[DataProvider('verdicts')]
    public function it_judges_a_step(Verdict $verdict, ?string $feedback, ?FeedbackStatus $feedbackStatus, int $events): void
    {
        Event::fake();
        $this->freezeSecond();
        app()->setLocale('nl');

        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();
        $step = SessionStep::factory()->for($session)->create([
            'verdict' => Verdict::Feedback,
            'feedback' => '::earlier-feedback::',
            'feedback_status' => FeedbackStatus::Open,
        ]);

        $action = app(JudgeStep::class);
        $action->judge($step, new VerdictData([
            'verdict' => $verdict,
            'feedback' => '::feedback::',
            'page_url' => 'https://example.com/orders',
            'user_agent' => '::user-agent::',
            'viewport_width' => 1280,
            'viewport_height' => 720,
        ]));

        $step->refresh();

        $this->assertSame($verdict, $step->verdict);
        $this->assertSame($feedback, $step->feedback);
        $this->assertSame($feedbackStatus, $step->feedback_status);
        $this->assertSame('https://example.com/orders', $step->page_url);
        $this->assertSame('::user-agent::', $step->user_agent);
        $this->assertSame([1280, 720], [$step->viewport_width, $step->viewport_height]);
        $this->assertSame('nl', $step->app_locale);
        $this->assertSame(now()->toDateTimeString(), $step->judged_at?->toDateTimeString());

        Event::assertDispatchedTimes(FeedbackSubmittedEvent::class, $events);
    }

    public static function verdicts(): Iterator
    {
        yield 'accepted' => [Verdict::Accepted, null, null, 0];
        yield 'feedback' => [Verdict::Feedback, '::feedback::', FeedbackStatus::Open, 1];
    }
}
