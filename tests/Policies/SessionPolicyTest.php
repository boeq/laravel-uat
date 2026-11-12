<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Policies;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Gate;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SessionPolicyTest extends TestCase
{
    #[Test]
    public function it_limits_sessions_to_their_tester(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        $this->assertTrue(Gate::forUser($tester)->allows('update', $session));
        $this->assertFalse(Gate::forUser(Tester::query()->create())->allows('update', $session));
    }

    #[Test]
    #[DataProvider('statuses')]
    public function it_locks_finished_sessions(SessionStatus $status, bool $allowed): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create(['status' => $status]);

        $this->assertSame($allowed, Gate::forUser($tester)->allows('update', $session));
    }

    public static function statuses(): Iterator
    {
        yield 'in progress' => [SessionStatus::InProgress, true];
        yield 'paused' => [SessionStatus::Paused, true];
        yield 'completed' => [SessionStatus::Completed, false];
        yield 'abandoned' => [SessionStatus::Abandoned, false];
    }

    #[Test]
    #[DataProvider('verdicts')]
    public function it_completes_sessions_without_open_steps(?Verdict $verdict, bool $allowed): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        SessionStep::factory()->for($session)->create(['verdict' => $verdict]);

        $this->assertSame($allowed, Gate::forUser($tester)->allows('complete', $session));
    }

    public static function verdicts(): Iterator
    {
        yield 'judged' => [Verdict::Feedback, true];
        yield 'not judged' => [null, false];
    }
}
