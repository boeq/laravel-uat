<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Contracts\Scenarios\SavesTranslation;
use Boeq\Uat\Data\TranslationData;
use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioTranslationControllerTest extends TestCase
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
    public function it_saves_a_translation(): void
    {
        $scenario = Scenario::factory()->create();

        $this->mock(SavesTranslation::class, function (MockInterface $mock) use ($scenario): void {
            $mock->shouldReceive('save')
                ->withArgs(fn (Scenario $given, TranslationData $data): bool => $given->is($scenario)
                    && $data->locale === 'nl'
                    && $data->title === '::title::')
                ->once();
        });

        $this
            ->putJson(route('uat.api.scenarios.translations.update', [$scenario, 'nl']), [
                'title' => '::title::',
                'description' => '::description::',
                'steps' => [],
            ])
            ->assertNoContent();
    }

    #[Test]
    #[DataProvider('invalid_translations')]
    public function it_validates_a_translation(string $locale, array $steps, string $field, string $message): void
    {
        $scenario = Scenario::factory()->create();

        Step::factory()->create(['scenario_id' => $scenario->id]);
        Step::factory()->create();

        $this
            ->putJson(route('uat.api.scenarios.translations.update', [$scenario, $locale]), [
                'title' => '::title::',
                'description' => '::description::',
                'steps' => $steps,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $message]);
    }

    public static function invalid_translations(): Iterator
    {
        yield 'unknown language' => [
            'de',
            [],
            'locale',
            'Translations may only use these languages: en, nl.',
        ];

        yield 'missing step text' => [
            'nl',
            [['id' => 1, 'title' => '::title::', 'expected_result' => '::result::']],
            'steps.0.instruction',
            'Step 1 is missing instruction.',
        ];

        yield 'step of another scenario' => [
            'nl',
            [['id' => 2, 'title' => '::title::', 'instruction' => '::instruction::', 'expected_result' => '::result::']],
            'steps.0.id',
            'Step 1 does not belong to this scenario.',
        ];
    }
}
