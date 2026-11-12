<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Sessions;

use Boeq\Uat\Actions\Sessions\AcceptSession;
use Boeq\Uat\Contracts\Sessions\CompletesSession;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

final class AcceptSessionTest extends TestCase
{
    #[Test]
    public function it_accepts_the_steps_without_a_verdict(): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();
        $other = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        SessionStep::factory()->for($session)->create(['position' => 1, 'verdict' => Verdict::Feedback]);
        SessionStep::factory()->for($session)->create(['position' => 2]);
        SessionStep::factory()->for($other)->create();

        $this->mock(CompletesSession::class, function (MockInterface $mock) use ($session): void {
            $mock->shouldReceive('complete')
                ->withArgs(fn (Session $given, ?string $closingComment): bool => $given->is($session) && $closingComment === null)
                ->once();
        });

        $action = app(AcceptSession::class);
        $action->accept($session);

        $this->assertSame([Verdict::Feedback, Verdict::Accepted], $session->steps->pluck('verdict')->all());
        $this->assertNull($other->steps->sole()->verdict);
    }
}
