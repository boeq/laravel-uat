<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Actions\Scenarios;

use Boeq\Uat\Actions\Scenarios\SaveTranslation;
use Boeq\Uat\Data\TranslationData;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SaveTranslationTest extends TestCase
{
    #[Test]
    public function it_saves_a_translation(): void
    {
        $scenario = Scenario::factory()->create();
        $step = Step::factory()->create(['scenario_id' => $scenario->id]);

        $action = app(SaveTranslation::class);
        $action->save($scenario, new TranslationData([
            'locale' => 'nl',
            'title' => '::title::',
            'description' => '::description::',
            'steps' => [
                [
                    'id' => $step->id,
                    'title' => '::step-title::',
                    'instruction' => '::instruction::',
                    'expected_result' => '::result::',
                    'url' => '/nl/bestellingen',
                ],
            ],
        ]));

        $this->assertSame('::title::', $scenario->translation('nl')->title);
        $this->assertSame('::step-title::', $step->translation('nl')->title);
        $this->assertSame('/nl/bestellingen', $step->translation('nl')->url);
    }
}
