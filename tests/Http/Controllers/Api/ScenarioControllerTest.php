<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Contracts\Scenarios\SavesScenario;
use Boeq\Uat\Data\ScenarioData;
use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Models\StepTranslation;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('uat.locales', ['en', 'nl']);

        ApiToken::factory()->create([
            'token' => hash('sha256', '::token::'),
            'abilities' => [TokenAbility::Read, TokenAbility::Write],
        ]);

        $this->withToken('::token::');
    }

    #[Test]
    #[DataProvider('filters')]
    public function it_lists_scenarios(array $filters, int $count): void
    {
        Scenario::factory()->create();
        Scenario::factory()->active()->create();

        $this
            ->getJson(route('uat.api.scenarios.index', $filters))
            ->assertOk()
            ->assertJsonCount($count, 'data');
    }

    public static function filters(): Iterator
    {
        yield 'all' => [[], 2];
        yield 'active' => [['status' => 'active'], 1];
    }

    #[Test]
    public function it_shows_a_scenario(): void
    {
        $scenario = Scenario::factory()->create(['start_url' => '/orders']);
        ScenarioTranslation::factory()->create([
            'scenario_id' => $scenario->id,
            'title' => '::title::',
            'description' => '::description::',
        ]);

        $step = Step::factory()->create(['scenario_id' => $scenario->id, 'url' => '/orders/create']);
        StepTranslation::factory()->create([
            'step_id' => $step->id,
            'title' => '::step-title::',
            'instruction' => '::instruction::',
            'expected_result' => '::expected-result::',
        ]);

        $tester = Tester::query()->create();

        $completed = Session::factory()->for($tester, 'tester')->for($scenario)->create(['status' => SessionStatus::Completed]);
        SessionStep::factory()->for($completed)->create(['verdict' => Verdict::Accepted]);
        SessionStep::factory()->for($completed)->create(['verdict' => Verdict::Feedback, 'feedback_status' => FeedbackStatus::Open]);
        SessionStep::factory()->for($completed)->create(['verdict' => Verdict::Feedback, 'feedback_status' => FeedbackStatus::Resolved]);

        Session::factory()->for($tester, 'tester')->for($scenario)->create();

        $this
            ->getJson(route('uat.api.scenarios.show', $scenario))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $scenario->id,
                    'status' => 'draft',
                    'start_url' => '/orders',
                    'ready_for_retest_at' => null,
                    'translations' => [
                        'en' => [
                            'title' => '::title::',
                            'description' => '::description::',
                            'preconditions' => null,
                            'start_url' => null,
                        ],
                    ],
                    'steps' => [
                        [
                            'id' => $step->id,
                            'position' => 1,
                            'url' => '/orders/create',
                            'translations' => [
                                'en' => [
                                    'title' => '::step-title::',
                                    'instruction' => '::instruction::',
                                    'expected_result' => '::expected-result::',
                                    'link_label' => null,
                                    'url' => null,
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        'sessions_count' => 2,
                        'completed_sessions_count' => 1,
                        'accepted_steps_count' => 1,
                        'feedback_steps_count' => 2,
                        'open_feedback_count' => 1,
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_stores_a_scenario(): void
    {
        $this->mock(SavesScenario::class, function (MockInterface $mock): void {
            $mock->shouldReceive('save')
                ->withArgs(fn (Scenario $scenario, ScenarioData $data): bool => ! $scenario->exists
                    && $data->start_url === '/orders')
                ->once()
                ->andReturnUsing(fn (Scenario $scenario): bool => $scenario->save());
        });

        $this
            ->postJson(route('uat.api.scenarios.store'), [
                'start_url' => '/orders',
                'translations' => [],
                'steps' => [],
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'draft');
    }

    #[Test]
    #[DataProvider('invalid_scenarios')]
    public function it_validates_a_scenario(array $scenario, string $field, string $message): void
    {
        $this
            ->postJson(route('uat.api.scenarios.store'), $scenario)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $message]);
    }

    public static function invalid_scenarios(): Iterator
    {
        yield 'missing step text' => [
            [
                'translations' => [],
                'steps' => [
                    ['translations' => ['en' => ['title' => '::title::', 'instruction' => '::instruction::', 'expected_result' => '::result::']]],
                    ['translations' => ['nl' => ['title' => '::title::', 'expected_result' => '::result::']]],
                ],
            ],
            'steps.1.translations.nl.instruction',
            'Step 2 is missing nl.instruction.',
        ];

        yield 'missing scenario text' => [
            ['translations' => ['en' => ['description' => '::description::']], 'steps' => []],
            'translations.en.title',
            'The scenario is missing en.title.',
        ];

        yield 'unknown language' => [
            ['translations' => ['de' => ['title' => '::title::', 'description' => '::description::']], 'steps' => []],
            'translations',
            'Translations may only use these languages: en, nl.',
        ];

        yield 'unknown step language' => [
            ['translations' => [], 'steps' => [['translations' => ['de' => []]]]],
            'steps.0.translations',
            'Step 1 may only use these languages: en, nl.',
        ];
    }

    #[Test]
    public function it_updates_a_scenario(): void
    {
        $scenario = Scenario::factory()->create();

        $this->mock(SavesScenario::class, function (MockInterface $mock) use ($scenario): void {
            $mock->shouldReceive('save')
                ->withArgs(fn (Scenario $given, ScenarioData $data): bool => $given->is($scenario)
                    && $data->start_url === '/orders')
                ->once();
        });

        $this
            ->putJson(route('uat.api.scenarios.update', $scenario), [
                'start_url' => '/orders',
                'translations' => [],
                'steps' => [],
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $scenario->id);
    }

    #[Test]
    public function it_keeps_an_active_scenario_complete(): void
    {
        $scenario = Scenario::factory()->active()->create();

        $this
            ->putJson(route('uat.api.scenarios.update', $scenario), [
                'translations' => [],
                'steps' => [['translations' => []]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'translations.en' => 'An active scenario needs its en translation.',
                'steps.0.translations.en' => 'Step 1 of an active scenario needs its en translation.',
            ]);
    }

    #[Test]
    public function it_requires_steps_on_an_active_scenario(): void
    {
        $scenario = Scenario::factory()->active()->create();

        $this
            ->putJson(route('uat.api.scenarios.update', $scenario), [
                'translations' => ['en' => ['title' => '::title::', 'description' => '::description::']],
                'steps' => [],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['steps' => 'An active scenario needs at least one step.']);
    }

    #[Test]
    public function it_deletes_a_scenario(): void
    {
        $scenario = Scenario::factory()->create();

        $this
            ->deleteJson(route('uat.api.scenarios.destroy', $scenario))
            ->assertNoContent();

        $this->assertModelMissing($scenario);
    }

    #[Test]
    public function it_keeps_a_scenario_with_sessions(): void
    {
        $scenario = Scenario::factory()->create();

        Session::factory()->for($scenario)->for(Tester::query()->create(), 'tester')->create();

        $this
            ->deleteJson(route('uat.api.scenarios.destroy', $scenario))
            ->assertConflict()
            ->assertJsonPath('message', 'A scenario with sessions can only be archived.');

        $this->assertModelExists($scenario);
    }
}
