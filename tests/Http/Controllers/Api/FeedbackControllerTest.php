<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Enums\FeedbackStatus;
use Boeq\Uat\Enums\Verdict;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\SessionStep;
use Boeq\Uat\Tests\Fakes\Tester;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class FeedbackControllerTest extends TestCase
{
    #[Test]
    #[DataProvider('filters')]
    public function it_lists_feedback(array $filters, array $feedback): void
    {
        ApiToken::factory()->create(['token' => hash('sha256', '::token::')]);

        $tester = Tester::query()->create();

        $first = Session::factory()->for($tester, 'tester')->for(Scenario::factory())->create();
        $second = Session::factory()->for($tester, 'tester')->for(Scenario::factory())->create();

        SessionStep::factory()->for($first)->create(['verdict' => Verdict::Accepted]);
        SessionStep::factory()->for($first)->create([
            'verdict' => Verdict::Feedback,
            'feedback' => '::open::',
            'feedback_status' => FeedbackStatus::Open,
        ]);
        SessionStep::factory()->for($first)->create([
            'verdict' => Verdict::Feedback,
            'feedback' => '::resolved::',
            'feedback_status' => FeedbackStatus::Resolved,
        ]);
        SessionStep::factory()->for($second)->create([
            'verdict' => Verdict::Feedback,
            'feedback' => '::other-scenario::',
            'feedback_status' => FeedbackStatus::Open,
        ]);

        $this
            ->withToken('::token::')
            ->getJson(route('uat.api.feedback.index', $filters))
            ->assertOk()
            ->assertJsonPath('data.*.feedback', $feedback);
    }

    public static function filters(): Iterator
    {
        yield 'open by default' => [[], ['::other-scenario::', '::open::']];
        yield 'resolved' => [['status' => 'resolved'], ['::resolved::']];
        yield 'one scenario' => [['scenario' => 1], ['::open::']];
    }

    #[Test]
    public function it_describes_feedback(): void
    {
        ApiToken::factory()->create(['token' => hash('sha256', '::token::')]);

        $tester = Tester::query()->create();
        $session = Session::factory()->for($tester, 'tester')->create(['locale' => 'nl']);

        $step = SessionStep::factory()->for($session)->create([
            'title' => '::title::',
            'instruction' => '::instruction::',
            'expected_result' => '::expected-result::',
            'url' => '/orders',
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
            ->withToken('::token::')
            ->getJson(route('uat.api.feedback.index'))
            ->assertOk()
            ->assertJsonPath('data.0', [
                'id' => $step->id,
                'scenario_id' => $session->scenario_id,
                'session_id' => $session->id,
                'tester' => [
                    'type' => Tester::class,
                    'id' => $tester->id,
                ],
                'locale' => 'nl',
                'step' => [
                    'position' => 1,
                    'title' => '::title::',
                    'instruction' => '::instruction::',
                    'expected_result' => '::expected-result::',
                    'url' => '/orders',
                ],
                'feedback' => '::feedback::',
                'status' => 'open',
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
            ]);
    }
}
