<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScenarioTranslation> */
class ScenarioTranslationFactory extends Factory
{
    protected $model = ScenarioTranslation::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'locale' => 'en',
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
        ];
    }
}
