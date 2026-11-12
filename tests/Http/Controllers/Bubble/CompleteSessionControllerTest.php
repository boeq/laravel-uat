<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\CompletesSession;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

final class CompleteSessionControllerTest extends TestCase
{
    #[Test]
    public function it_completes_a_session(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        $this->mock(CompletesSession::class, function (MockInterface $mock) use ($session): void {
            $mock->shouldReceive('complete')
                ->withArgs(fn (Session $given, ?string $closingComment): bool => $given->is($session)
                    && $closingComment === '::closing-comment::')
                ->once();
        });

        $this
            ->actingAs($tester)
            ->post(route('uat.sessions.complete', $session), ['closing_comment' => '::closing-comment::'])
            ->assertRedirect(route('uat.scenarios.index'));
    }

    #[Test]
    public function it_requires_a_verdict_on_every_step(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        SessionStep::factory()->for($session)->create();

        $this
            ->actingAs($tester)
            ->post(route('uat.sessions.complete', $session))
            ->assertForbidden();
    }
}
