<?php

declare(strict_types=1);

namespace Boeq\Uat\Commands\Tokens;

use Boeq\Uat\Contracts\Tokens\CreatesApiToken;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreateTokenCommand extends Command
{
    protected $signature = 'uat:create-token
        {name : A name to recognise the token by}
        {--write : Allow the token to change scenarios and feedback}
        {--expires-at= : The moment the token stops working}';

    protected $description = 'Create a token for the UAT API';

    public function handle(CreatesApiToken $action): int
    {
        /** @var string $name */
        $name = $this->argument('name');

        /** @var ?string $expiresAt */
        $expiresAt = $this->option('expires-at');

        if (ApiToken::query()->where('name', $name)->exists()) {
            $this->error('A token named "'.$name.'" already exists.');

            return static::FAILURE;
        }

        /** @var Collection<int, TokenAbility> $abilities */
        $abilities = collect([TokenAbility::Read]);

        if ($this->option('write')) {
            $abilities->push(TokenAbility::Write);
        }

        $token = $action->create($name, $abilities, $expiresAt === null ? null : Carbon::parse($expiresAt));

        $this->info('This token is shown once, store it now:');
        $this->line($token);

        return static::SUCCESS;
    }
}
