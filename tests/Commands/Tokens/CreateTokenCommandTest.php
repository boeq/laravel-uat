<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Commands\Tokens;

use Boeq\Uat\Commands\Tokens\CreateTokenCommand;
use Boeq\Uat\Contracts\Tokens\CreatesApiToken;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Tests\TestCase;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Testing\PendingCommand;
use Iterator;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CreateTokenCommandTest extends TestCase
{
    #[Test]
    #[DataProvider('token_options')]
    public function it_creates_a_token(array $options, array $abilities, ?string $expiresAt): void
    {
        $this->mock(CreatesApiToken::class, function (MockInterface $mock) use ($abilities, $expiresAt): void {
            $mock->shouldReceive('create')
                ->withArgs(fn (string $name, Collection $given, ?CarbonInterface $expires): bool => $name === '::name::'
                    && $given->all() === $abilities
                    && $expires?->toDateString() === $expiresAt)
                ->once()
                ->andReturn('::token::');
        });

        /** @var PendingCommand $command */
        $command = $this->artisan(CreateTokenCommand::class, ['name' => '::name::', ...$options]);

        $command
            ->expectsOutput('::token::')
            ->assertSuccessful()
            ->run();
    }

    public static function token_options(): Iterator
    {
        yield 'read' => [[], [TokenAbility::Read], null];
        yield 'write' => [['--write' => true], [TokenAbility::Read, TokenAbility::Write], null];
        yield 'expiring' => [['--expires-at' => '2027-01-01'], [TokenAbility::Read], '2027-01-01'];
    }

    #[Test]
    public function it_rejects_a_name_that_is_taken(): void
    {
        ApiToken::factory()->create(['name' => '::name::']);

        /** @var PendingCommand $command */
        $command = $this->artisan(CreateTokenCommand::class, ['name' => '::name::']);

        $command
            ->expectsOutput('A token named "::name::" already exists.')
            ->assertFailed()
            ->run();

        $this->assertSame(1, ApiToken::query()->count());
    }
}
