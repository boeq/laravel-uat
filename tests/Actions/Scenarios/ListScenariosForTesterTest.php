<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Scenarios;

use Boeq\Uat\Actions\Scenarios\ListScenariosForTester;
use Boeq\Uat\Data\ScenarioProgressData;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Tests\Fakes\RestrictedTester;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ListScenariosForTesterTest extends TestCase
{
    /** @param class-string<Tester> $tester */
    #[Test]
    #[DataProvider('users')]
    public function it_lists_the_scenarios_a_tester_may_test(string $tester, int $count): void
    {
        Scenario::factory()->active()->create();
        Scenario::factory()->create();

        $action = app(ListScenariosForTester::class);

        $this->assertCount($count, $action->list($tester::query()->create()));
    }

    public static function users(): Iterator
    {
        yield 'tester' => [Tester::class, 1];
        yield 'tester the contract restricts' => [RestrictedTester::class, 0];
    }

    #[Test]
    public function it_gives_the_latest_session_of_the_tester(): void
    {
        $tester = Tester::query()->create();

        $tested = Scenario::factory()->active()->create();
        Session::factory()->for($tester, 'tester')->for($tested)->create(['status' => SessionStatus::Completed]);
        $latest = Session::factory()->for($tester, 'tester')->for($tested)->create(['status' => SessionStatus::Completed]);
        Session::factory()->for($tester, 'tester')->for($tested)->create(['status' => SessionStatus::Abandoned]);
        Session::factory()->for(Tester::query()->create(), 'tester')->for($tested)->create();

        $untested = Scenario::factory()->active()->create();

        $action = app(ListScenariosForTester::class);
        $scenarios = $action->list($tester);

        $this->assertSame(
            [[$tested->id, $latest->id], [$untested->id, null]],
            $scenarios->map(fn (ScenarioProgressData $progress): array => [$progress->scenario->id, $progress->session?->id])->all(),
        );
    }
}
