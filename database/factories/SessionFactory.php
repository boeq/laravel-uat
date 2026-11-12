<?php

declare(strict_types=1);

namespace Boeq\Uat\Database\Factories;

use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Session> */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'locale' => 'en',
            'status' => SessionStatus::InProgress,
        ];
    }
}
