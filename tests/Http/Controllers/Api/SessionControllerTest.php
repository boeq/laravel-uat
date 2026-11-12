<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SessionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ApiToken::factory()->create(['token' => hash('sha256', '::token::')]);

        $this->withToken('::token::');
    }

    #[Test]
    public function it_lists_the_sessions_of_a_scenario(): void
    {
        $tester = Tester::query()->create();
        $scenario = Scenario::factory()->create();

        $first = Session::factory()->for($tester, 'tester')->for($scenario)->create();
        $second = Session::factory()->for($tester, 'tester')->for($scenario)->create();
        Session::factory()->for($tester, 'tester')->create();

        $this
            ->getJson(route('uat.api.scenarios.sessions.index', $scenario))
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('data.*.id', [$second->id, $first->id]);
    }

    #[Test]
    public function it_shows_a_session(): void
    {
        $tester = Tester::query()->create();

        $session = Session::factory()->for($tester, 'tester')->create([
            'locale' => 'nl',
            'status' => SessionStatus::Completed,
            'closing_comment' => '::closing-comment::',
        ]);

        $step = SessionStep::factory()->for($session)->create([
            'title' => '::title::',
            'instruction' => '::instruction::',
            'expected_result' => '::expected-result::',
            'verdict' => Verdict::Feedback,
            'feedback' => '::feedback::',
            'feedback_status' => FeedbackStatus::Open,
            'page_url' => 'https://example.com/orders',
            'user_agent' => '::user-agent::',
            'viewport_width' => 1280,
            'viewport_height' => 720,
            'app_locale' => 'nl',
        ]);

        $this
            ->getJson(route('uat.api.sessions.show', $session))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $session->id,
                    'scenario_id' => $session->scenario_id,
                    'tester' => [
                        'type' => Tester::class,
                        'id' => $tester->id,
                    ],
                    'locale' => 'nl',
                    'status' => 'completed',
                    'result' => 'accepted_with_feedback',
                    'closing_comment' => '::closing-comment::',
                    'steps' => [
                        [
                            'id' => $step->id,
                            'step_id' => null,
                            'position' => 1,
                            'title' => '::title::',
                            'instruction' => '::instruction::',
                            'expected_result' => '::expected-result::',
                            'link_label' => null,
                            'url' => null,
                            'verdict' => 'feedback',
                            'feedback' => '::feedback::',
                            'feedback_status' => 'open',
                            'resolution_note' => null,
                            'resolved_at' => null,
                            'context' => [
                                'page_url' => 'https://example.com/orders',
                                'user_agent' => '::user-agent::',
                                'viewport_width' => 1280,
                                'viewport_height' => 720,
                                'app_locale' => 'nl',
                                'judged_at' => null,
                            ],
                        ],
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_has_no_result_for_an_unfinished_session(): void
    {
        $session = Session::factory()->for(Tester::query()->create(), 'tester')->create();

        $this
            ->getJson(route('uat.api.sessions.show', $session))
            ->assertOk()
            ->assertJsonPath('data.result', null);
    }
}
