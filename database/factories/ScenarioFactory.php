<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Scenario> */
class ScenarioFactory extends Factory
{
    protected $model = Scenario::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'status' => ScenarioStatus::Draft,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => ScenarioStatus::Active]);
    }
}
