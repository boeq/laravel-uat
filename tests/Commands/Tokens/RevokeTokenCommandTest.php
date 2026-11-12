<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Commands\Tokens;

use Boeq\Uat\Commands\Tokens\RevokeTokenCommand;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Testing\PendingCommand;
use PHPUnit\Framework\Attributes\Test;

final class RevokeTokenCommandTest extends TestCase
{
    #[Test]
    public function it_revokes_a_token(): void
    {
        ApiToken::factory()->create(['name' => '::name::']);

        /** @var PendingCommand $command */
        $command = $this->artisan(RevokeTokenCommand::class, ['name' => '::name::']);

        $command->assertSuccessful()->run();

        $this->assertSame(0, ApiToken::query()->count());
    }

    #[Test]
    public function it_fails_on_an_unknown_name(): void
    {
        /** @var PendingCommand $command */
        $command = $this->artisan(RevokeTokenCommand::class, ['name' => '::name::']);

        $command
            ->expectsOutput('There is no token named "::name::".')
            ->assertFailed()
            ->run();
    }
}
