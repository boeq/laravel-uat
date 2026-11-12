<?php

declare(strict_types=1);

namespace Boeq\Uat\Contracts\Access;

use Boeq\Uat\Contracts\TestsFeatures;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface ResolvesTester
{
    public function resolve(?Authenticatable $user): (TestsFeatures&Model)|null;
}
