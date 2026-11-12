<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioRetestControllerTest extends TestCase
{
    #[Test]
    public function it_flags_a_scenario_ready_for_retest(): void
    {
        $this->freezeSecond();

        ApiToken::factory()->create([
            'token' => hash('sha256', '::token::'),
            'abilities' => [TokenAbility::Read, TokenAbility::Write],
        ]);

        $scenario = Scenario::factory()->active()->create();

        $this
            ->withToken('::token::')
            ->postJson(route('uat.api.scenarios.retest', $scenario))
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $scenario->id,
                    'ready_for_retest_at' => now()->toJSON(),
                ],
            ]);

        $this->assertSame(now()->toDateTimeString(), $scenario->refresh()->ready_for_retest_at?->toDateTimeString());
    }
}
