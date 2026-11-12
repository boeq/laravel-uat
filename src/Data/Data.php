<?php

declare(strict_types=1);

namespace Boeq\Uat\Data;

use Illuminate\Support\Fluent;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends Fluent<TKey, TValue>
 */
abstract class Data extends Fluent
{
    //
}
