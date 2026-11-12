<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Models\Step;
use Boeq\Uat\Models\StepTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StepTranslation> */
class StepTranslationFactory extends Factory
{
    protected $model = StepTranslation::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'step_id' => Step::factory(),
            'locale' => 'en',
            'title' => fake()->sentence(3),
            'instruction' => fake()->sentence(),
            'expected_result' => fake()->sentence(),
        ];
    }
}
