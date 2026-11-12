<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Step> */
class StepFactory extends Factory
{
    protected $model = Step::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'position' => 1,
        ];
    }
}
