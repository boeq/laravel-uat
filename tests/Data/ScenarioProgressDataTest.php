<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Data;

use Boeq\Uat\Data\ScenarioProgressData;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioProgressDataTest extends TestCase
{
    #[Test]
    #[DataProvider('sessions')]
    public function it_tracks_the_progress(?array $session, ?string $readyForRetestAt, bool $needsRetest, bool $finished): void
    {
        $progress = new ScenarioProgressData([
            'scenario' => new Scenario(['ready_for_retest_at' => $readyForRetestAt]),
            'session' => $session === null ? null : new Session($session),
        ]);

        $this->assertSame($needsRetest, $progress->needsRetest());
        $this->assertSame($finished, $progress->isFinished());
    }

    public static function sessions(): Iterator
    {
        $completed = ['status' => SessionStatus::Completed, 'completed_at' => '2026-11-10'];

        yield 'not started' => [null, '2026-11-11', false, false];
        yield 'in progress' => [['status' => SessionStatus::InProgress], '2026-11-11', false, false];
        yield 'completed' => [$completed, null, false, true];
        yield 'completed after the retest request' => [$completed, '2026-11-09', false, true];
        yield 'completed before the retest request' => [$completed, '2026-11-11', true, false];
    }
}
