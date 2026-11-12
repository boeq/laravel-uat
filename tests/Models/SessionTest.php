<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Models;

use Boeq\Uat\Enums\SessionResult;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SessionTest extends TestCase
{
    #[Test]
    #[DataProvider('verdicts')]
    public function it_derives_the_result(Verdict $verdict, SessionResult $result): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        SessionStep::factory()->for($session)->create(['position' => 1, 'verdict' => Verdict::Accepted]);
        SessionStep::factory()->for($session)->create(['position' => 2, 'verdict' => $verdict]);

        $this->assertSame($result, $session->result());
    }

    public static function verdicts(): Iterator
    {
        yield 'all accepted' => [Verdict::Accepted, SessionResult::Accepted];
        yield 'with feedback' => [Verdict::Feedback, SessionResult::AcceptedWithFeedback];
    }

    #[Test]
    public function it_finds_the_current_step(): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        SessionStep::factory()->for($session)->create(['position' => 1, 'verdict' => Verdict::Accepted]);
        $current = SessionStep::factory()->for($session)->create(['position' => 2]);

        $this->assertTrue($current->is($session->currentStep()));
    }

    #[Test]
    #[DataProvider('statuses')]
    public function it_scopes_unfinished_sessions(SessionStatus $status, bool $unfinished): void
    {
        Session::factory()->for(Tester::query()->create(), 'tester')->create(['status' => $status]);

        $this->assertSame($unfinished, Session::query()->unfinished()->exists());
    }

    public static function statuses(): Iterator
    {
        yield 'in progress' => [SessionStatus::InProgress, true];
        yield 'paused' => [SessionStatus::Paused, true];
        yield 'completed' => [SessionStatus::Completed, false];
        yield 'abandoned' => [SessionStatus::Abandoned, false];
    }
}
