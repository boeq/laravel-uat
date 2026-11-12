<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Access;

use Boeq\Uat\Contracts\Access\ResolvesTester;
use Boeq\Uat\Contracts\TestsFeatures;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class ResolveTester implements ResolvesTester
{
    public function resolve(?Authenticatable $user): (TestsFeatures&Model)|null
    {
        if (! config()->boolean('uat.enabled')) {
            return null;
        }

        if (! $user instanceof TestsFeatures || ! $user instanceof Model) {
            return null;
        }

        return $user->canTestFeatures() ? $user : null;
    }

    public static function bind(): void
    {
        app()->singleton(ResolvesTester::class, static::class);
    }
}
