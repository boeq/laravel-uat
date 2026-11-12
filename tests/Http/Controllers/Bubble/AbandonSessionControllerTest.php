<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class AbandonSessionControllerTest extends TestCase
{
    #[Test]
    public function it_abandons_a_session(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        $this
            ->actingAs($tester)
            ->post(route('uat.sessions.abandon', $session))
            ->assertRedirect(route('uat.scenarios.index'));

        $this->assertSame(SessionStatus::Abandoned, $session->refresh()->status);
    }
}
