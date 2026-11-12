<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Boeq\Uat\Contracts\Scenarios\ChangesScenarioStatus;
use Boeq\Uat\Contracts\Scenarios\SavesScenario;
use Boeq\Uat\Data\ScenarioData;
use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Illuminate\Database\Seeder;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(SavesScenario $savesScenario, ChangesScenarioStatus $changesScenarioStatus): void
    {
        User::query()->create([
            'name' => 'Demo tester',
            'email' => 'tester@example.com',
            'password' => bcrypt('password'),
        ]);

        // A fixed token keeps the demo commands in the README copyable. Never do this outside a demo.
        ApiToken::query()->create([
            'name' => 'Demo',
            'token' => hash('sha256', 'demo-token'),
            'abilities' => [TokenAbility::Read, TokenAbility::Write],
        ]);

        $scenario = new Scenario(['status' => ScenarioStatus::Draft]);

        $savesScenario->save($scenario, new ScenarioData([
            'start_url' => '/orders',
            'translations' => [
                'en' => [
                    'title' => 'Create an order',
                    'description' => 'Check that a new order can be created from the order overview.',
                    'preconditions' => 'You are logged in as the demo tester.',
                ],
                'nl' => [
                    'title' => 'Een bestelling aanmaken',
                    'description' => 'Controleer of je vanuit het besteloverzicht een nieuwe bestelling kunt aanmaken.',
                    'preconditions' => 'Je bent ingelogd als de demotester.',
                ],
            ],
            'steps' => [
                [
                    'url' => '/orders',
                    'translations' => [
                        'en' => [
                            'title' => 'Open the order overview',
                            'instruction' => 'Go to the order overview.',
                            'expected_result' => 'A list of orders with a "New order" button.',
                            'link_label' => 'Go to the orders',
                        ],
                        'nl' => [
                            'title' => 'Open het besteloverzicht',
                            'instruction' => 'Ga naar het besteloverzicht.',
                            'expected_result' => 'Een lijst met bestellingen en een knop "Nieuwe bestelling".',
                            'link_label' => 'Naar de bestellingen',
                        ],
                    ],
                ],
                [
                    'url' => '/orders/create',
                    'translations' => [
                        'en' => [
                            'title' => 'Open the order form',
                            'instruction' => 'Click "New order".',
                            'expected_result' => 'An empty order form opens.',
                        ],
                        'nl' => [
                            'title' => 'Open het bestelformulier',
                            'instruction' => 'Klik op "Nieuwe bestelling".',
                            'expected_result' => 'Er opent een leeg bestelformulier.',
                        ],
                    ],
                ],
                [
                    'translations' => [
                        'en' => [
                            'title' => 'Save the order',
                            'instruction' => 'Enter the product "Demo chair" with quantity 2 and click "Save".',
                            'expected_result' => 'The order appears at the top of the order overview.',
                        ],
                        'nl' => [
                            'title' => 'Sla de bestelling op',
                            'instruction' => 'Vul het product "Demo chair" in met aantal 2 en klik op "Opslaan".',
                            'expected_result' => 'De bestelling staat bovenaan in het besteloverzicht.',
                        ],
                    ],
                ],
            ],
        ]));

        $changesScenarioStatus->change($scenario, ScenarioStatus::Active);
    }
}
