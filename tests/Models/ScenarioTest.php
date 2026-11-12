<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Models;

use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\ScenarioTranslation;
use Boeq\Uat\Tests\TestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ScenarioTest extends TestCase
{
    #[Test]
    public function it_prefixes_the_table(): void
    {
        config()->set('uat.table_prefix', 'acceptance_');

        $this->assertSame('acceptance_scenarios', (new Scenario)->getTable());
    }

    #[Test]
    #[DataProvider('locales')]
    public function it_translates(string $locale, string $title): void
    {
        $scenario = Scenario::factory()->create();

        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'locale' => 'en', 'title' => '::english::']);
        ScenarioTranslation::factory()->create(['scenario_id' => $scenario->id, 'locale' => 'nl', 'title' => '::dutch::']);

        $this->assertSame($title, $scenario->translation($locale)->title);
    }

    public static function locales(): Iterator
    {
        yield 'translated' => ['nl', '::dutch::'];
        yield 'default locale' => ['de', '::english::'];
    }
}
