<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Bubble;

use Boeq\Uat\Models\Session;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class BubbleControllerTest extends TestCase
{
    #[Test]
    public function it_opens_the_scenarios(): void
    {
        $this
            ->actingAs(Tester::query()->create())
            ->get(route('uat.bubble.show'))
            ->assertRedirect(route('uat.scenarios.index'));
    }

    #[Test]
    public function it_opens_the_session_in_progress(): void
    {
        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create();

        $this
            ->actingAs($tester)
            ->get(route('uat.bubble.show'))
            ->assertRedirect(route('uat.sessions.show', $session));
    }
}
