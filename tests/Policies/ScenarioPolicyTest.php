<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Policies;

use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Tests\Fakes\RestrictedTester;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Gate;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioPolicyTest extends TestCase
{
    #[Test]
    #[DataProvider('scenarios')]
    public function it_decides_who_tests_a_scenario(Tester $tester, ScenarioStatus $status, bool $allowed): void
    {
        $scenario = Scenario::factory()->create(['status' => $status]);

        $this->assertSame($allowed, Gate::forUser($tester)->allows('test', $scenario));
    }

    public static function scenarios(): Iterator
    {
        yield 'active' => [new Tester, ScenarioStatus::Active, true];
        yield 'draft' => [new Tester, ScenarioStatus::Draft, false];
        yield 'archived' => [new Tester, ScenarioStatus::Archived, false];
        yield 'denied by the contract' => [new RestrictedTester, ScenarioStatus::Active, false];
    }
}
