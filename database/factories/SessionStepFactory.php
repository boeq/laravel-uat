<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Models\SessionStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SessionStep> */
class SessionStepFactory extends Factory
{
    protected $model = SessionStep::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'position' => 1,
            'title' => fake()->sentence(3),
            'instruction' => fake()->sentence(),
            'expected_result' => fake()->sentence(),
        ];
    }
}
