<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Middleware;

use Boeq\Uat\Tests\Fakes\DeniedTester;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class AuthorizeTesterTest extends TestCase
{
    #[Test]
    public function it_hides_the_bubble_from_guests(): void
    {
        $this
            ->get(route('uat.bubble.show'))
            ->assertNotFound();
    }

    #[Test]
    #[DataProvider('users')]
    public function it_limits_the_bubble_to_testers(User $user, int $status): void
    {
        $this
            ->actingAs($user)
            ->get(route('uat.bubble.show'))
            ->assertStatus($status);
    }

    public static function users(): Iterator
    {
        yield 'tester' => [new Tester, 302];
        yield 'user without the contract' => [new User, 404];
        yield 'user the contract denies' => [new DeniedTester, 404];
    }
}
