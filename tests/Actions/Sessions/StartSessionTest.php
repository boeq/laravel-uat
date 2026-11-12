<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Sessions;

use Boeq\Uat\Actions\Sessions\StartSession;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Events\SessionStartedEvent;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Models\StepTranslation;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class StartSessionTest extends TestCase
{
    #[Test]
    #[DataProvider('locales')]
    public function it_starts_a_session(string $appLocale, string $locale, string $title, string $url): void
    {
        Event::fake();
        app()->setLocale($appLocale);

        $tester = Tester::query()->create();

        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'locale' => 'en']);
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'locale' => 'nl']);

        $step = Step::factory()->create(['scenario_id' => $scenario->id, 'url' => '/orders']);
        StepTranslation::factory()->create(['step_id' => $step->id, 'locale' => 'en', 'title' => '::english::']);
        StepTranslation::factory()->create(['step_id' => $step->id, 'locale' => 'nl', 'title' => '::dutch::', 'url' => '/bestellingen']);

        $action = app(StartSession::class);
        $session = $action->start($tester, $scenario);

        $this->assertSame($locale, $session->locale);
        $this->assertSame(SessionStatus::InProgress, $session->status);
        $this->assertTrue($session->tester()->is($tester));
        $this->assertSame(
            [[$step->id, 1, $title, $url, null]],
            $session->steps->map(fn (SessionStep $step): array => [$step->step_id, $step->position, $step->title, $step->url, $step->verdict])->all(),
        );

        Event::assertDispatched(SessionStartedEvent::class, fn (SessionStartedEvent $event): bool => $event->session->is($session));
    }

    public static function locales(): Iterator
    {
        yield 'translated' => ['nl', 'nl', '::dutch::', '/bestellingen'];
        yield 'default locale' => ['de', 'en', '::english::', '/orders'];
    }

    #[Test]
    public function it_pauses_the_session_in_progress(): void
    {
        $tester = Tester::query()->create();

        $paused = Session::factory()->for($tester, 'tester')->create();
        $untouched = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        $scenario = Scenario::factory()->active()->create();
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id]);

        $action = app(StartSession::class);
        $action->start($tester, $scenario);

        $this->assertSame(SessionStatus::Paused, $paused->refresh()->status);
        $this->assertSame(SessionStatus::InProgress, $untouched->refresh()->status);
    }

    #[Test]
    public function it_resumes_the_unfinished_session(): void
    {
        Event::fake();

        $tester = Tester::query()->create();
        $scenario = Scenario::factory()->active()->create();

        $unfinished = Session::factory()->for($tester, 'tester')->for($scenario)->create(['status' => SessionStatus::Paused]);

        $action = app(StartSession::class);
        $session = $action->start($tester, $scenario);

        $this->assertTrue($session->is($unfinished));
        $this->assertSame(SessionStatus::InProgress, $unfinished->refresh()->status);

        Event::assertNotDispatched(SessionStartedEvent::class);
    }
}
