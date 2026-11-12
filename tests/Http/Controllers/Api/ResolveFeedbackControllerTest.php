<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Contracts\Feedback\ResolvesFeedback;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

final class ResolveFeedbackControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ApiToken::factory()->create([
            'token' => hash('sha256', '::token::'),
            'abilities' => [TokenAbility::Read, TokenAbility::Write],
        ]);

        $this->withToken('::token::');
    }

    #[Test]
    public function it_resolves_feedback(): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();
        $step = SessionStep::factory()->for($session)->create(['verdict' => Verdict::Feedback]);

        $this->mock(ResolvesFeedback::class, function (MockInterface $mock) use ($step): void {
            $mock->shouldReceive('resolve')
                ->withArgs(fn (SessionStep $given, ?string $note): bool => $given->is($step) && $note === '::note::')
                ->once();
        });

        $this
            ->postJson(route('uat.api.feedback.resolve', $step), ['note' => '::note::'])
            ->assertNoContent();
    }

    #[Test]
    public function it_only_resolves_feedback(): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();
        $step = SessionStep::factory()->for($session)->create(['verdict' => Verdict::Accepted]);

        $this
            ->postJson(route('uat.api.feedback.resolve', $step))
            ->assertNotFound();
    }
}
