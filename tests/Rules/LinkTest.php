<?php

declare(strict_types=1);

namespace Boeq\Uat\Tests\Rules;

use Boeq\Uat\Rules\Link;
use Boeq\Uat\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LinkTest extends TestCase
{
    #[Test]
    #[DataProvider('links')]
    public function it_validates_links(mixed $link, bool $passes): void
    {
        $validator = Validator::make(['link' => $link], ['link' => [new Link]]);

        $this->assertSame($passes, $validator->passes());
    }

    public static function links(): Iterator
    {
        yield 'path' => ['/orders/create', true];
        yield 'https url' => ['https://example.com/orders', true];
        yield 'path without slash' => ['orders', false];
        yield 'protocol relative url' => ['//example.com', false];
        yield 'javascript url' => ['javascript:alert(1)', false];
        yield 'number' => [123, false];
    }
}
