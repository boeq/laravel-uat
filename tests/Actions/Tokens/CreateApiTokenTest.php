<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Tokens;

use Boeq\Uat\Actions\Tokens\CreateApiToken;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;

final class CreateApiTokenTest extends TestCase
{
    #[Test]
    public function it_creates_a_token(): void
    {
        /** @var Collection<int, TokenAbility> $abilities */
        $abilities = collect([TokenAbility::Read, TokenAbility::Write]);

        $action = app(CreateApiToken::class);
        $token = $action->create('::name::', $abilities, Carbon::parse('2027-01-01'));

        $apiToken = ApiToken::query()->where('name', '::name::')->sole();

        $this->assertSame(hash('sha256', $token), $apiToken->token);
        $this->assertSame([TokenAbility::Read, TokenAbility::Write], $apiToken->abilities->all());
        $this->assertSame('2027-01-01', $apiToken->expires_at?->toDateString());
    }
}
