<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Contracts\Scenarios\ChangesScenarioStatus;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioStatusControllerTest extends TestCase
{
    #[Test]
    public function it_changes_the_status(): void
    {
        ApiToken::factory()->create([
            'token' => hash('sha256', '::token::'),
            'abilities' => [TokenAbility::Read, TokenAbility::Write],
        ]);

        $scenario = Scenario::factory()->create();

        $this->mock(ChangesScenarioStatus::class, function (MockInterface $mock) use ($scenario): void {
            $mock->shouldReceive('change')
                ->withArgs(fn (Scenario $given, ScenarioStatus $status): bool => $given->is($scenario)
                    && $status === ScenarioStatus::Active)
                ->once()
                ->andReturn(collect(['::warning::']));
        });

        $this
            ->withToken('::token::')
            ->putJson(route('uat.api.scenarios.status.update', $scenario), ['status' => 'active'])
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $scenario->id,
                    'status' => 'draft',
                ],
                'warnings' => ['::warning::'],
            ]);
    }
}
