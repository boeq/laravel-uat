<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Middleware;

use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class AuthenticateApiTokenTest extends TestCase
{
    #[Test]
    #[DataProvider('abilities')]
    public function it_checks_the_ability(array $abilities, string $method, int $status, ?string $message): void
    {
        ApiToken::factory()->create(['token' => hash('sha256', '::token::'), 'abilities' => $abilities]);

        $this
            ->withToken('::token::')
            ->json($method, route('uat.api.scenarios.index'), ['translations' => [], 'steps' => []])
            ->assertStatus($status)
            ->assertJsonPath('message', $message);
    }

    public static function abilities(): Iterator
    {
        yield 'read token reads' => [[TokenAbility::Read], 'GET', 200, null];
        yield 'read token writes' => [[TokenAbility::Read], 'POST', 403, 'The bearer token may not write.'];
        yield 'write token writes' => [[TokenAbility::Read, TokenAbility::Write], 'POST', 201, null];
    }

    #[Test]
    #[DataProvider('invalid_tokens')]
    public function it_rejects_invalid_tokens(array $headers, string $message): void
    {
        ApiToken::factory()->create(['token' => hash('sha256', '::expired::'), 'expires_at' => now()->subDay()]);

        $this
            ->get(route('uat.api.configuration'), $headers)
            ->assertUnauthorized()
            ->assertJsonPath('message', $message);
    }

    public static function invalid_tokens(): Iterator
    {
        yield 'missing' => [[], 'A bearer token is required.'];
        yield 'unknown' => [['Authorization' => 'Bearer ::unknown::'], 'The bearer token is unknown or expired.'];
        yield 'expired' => [['Authorization' => 'Bearer ::expired::'], 'The bearer token is unknown or expired.'];
    }

    #[Test]
    public function it_records_the_last_use(): void
    {
        $this->freezeSecond();

        $token = ApiToken::factory()->create(['token' => hash('sha256', '::token::')]);

        $this
            ->withToken('::token::')
            ->getJson(route('uat.api.configuration'))
            ->assertOk();

        $this->assertSame(now()->toDateTimeString(), $token->refresh()->last_used_at?->toDateTimeString());
    }

    #[Test]
    public function it_hides_a_disabled_api(): void
    {
        config()->set('uat.api.enabled', false);

        ApiToken::factory()->create(['token' => hash('sha256', '::token::')]);

        $this
            ->withToken('::token::')
            ->getJson(route('uat.api.configuration'))
            ->assertNotFound();
    }
}
