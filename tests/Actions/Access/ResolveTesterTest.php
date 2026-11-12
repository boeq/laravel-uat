<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Access;

use Boeq\Uat\Actions\Access\ResolveTester;
use Boeq\Uat\Tests\Fakes\DeniedTester;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ResolveTesterTest extends TestCase
{
    #[Test]
    #[DataProvider('users')]
    public function it_resolves_the_tester(?Authenticatable $user, bool $enabled, bool $resolved): void
    {
        config()->set('uat.enabled', $enabled);

        $action = app(ResolveTester::class);

        $this->assertSame($resolved ? $user : null, $action->resolve($user));
    }

    public static function users(): Iterator
    {
        yield 'tester' => [new Tester, true, true];
        yield 'switched off' => [new Tester, false, false];
        yield 'guest' => [null, true, false];
        yield 'user without the contract' => [new User, true, false];
        yield 'user the contract denies' => [new DeniedTester, true, false];
    }
}
