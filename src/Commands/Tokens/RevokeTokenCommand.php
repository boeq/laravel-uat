<?php

declare(strict_types=1);

namespace Boeq\Uat\Commands\Tokens;

use Boeq\Uat\Models\ApiToken;
use Illuminate\Console\Command;

class RevokeTokenCommand extends Command
{
    protected $signature = 'uat:revoke-token {name : The name the token was created with}';

    protected $description = 'Revoke a token of the UAT API';

    public function handle(): int
    {
        /** @var string $name */
        $name = $this->argument('name');

        if (ApiToken::query()->where('name', $name)->delete() === 0) {
            $this->error('There is no token named "'.$name.'".');

            return static::FAILURE;
        }

        $this->info('The token "'.$name.'" is revoked.');

        return static::SUCCESS;
    }
}
