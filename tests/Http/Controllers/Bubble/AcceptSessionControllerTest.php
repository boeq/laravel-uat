<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Contracts\Sessions\AcceptsSession;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

final class AcceptSessionControllerTest extends TestCase
{
    #[Test]
    public function it_accepts_a_session(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        $this->mock(AcceptsSession::class, function (MockInterface $mock) use ($session): void {
            $mock->shouldReceive('accept')
                ->withArgs(fn (Session $given): bool => $given->is($session))
                ->once();
        });

        $this
            ->actingAs($tester)
            ->post(route('uat.sessions.accept', $session))
            ->assertRedirect(route('uat.scenarios.index'));
    }
}
