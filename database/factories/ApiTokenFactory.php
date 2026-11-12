<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ApiToken> */
class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'token' => hash('sha256', Str::random(40)),
            'abilities' => [TokenAbility::Read],
        ];
    }
}
