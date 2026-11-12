<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Http\Controllers\Api;

use Boeq\Uat\Models\ApiToken;
use Boeq\Uat\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ConfigurationControllerTest extends TestCase
{
    #[Test]
    public function it_returns_the_languages(): void
    {
        config()->set('uat.locales', ['en', 'nl']);
        config()->set('uat.default_locale', 'nl');

        ApiToken::factory()->create(['token' => hash('sha256', '::token::')]);

        $this
            ->withToken('::token::')
            ->getJson(route('uat.api.configuration'))
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'locales' => ['en', 'nl'],
                    'default_locale' => 'nl',
                ],
            ]);
    }
}
