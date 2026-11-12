<?php

declare(strict_types=1);

namespace Boeq\Uat\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class Link implements ValidationRule
{
    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && Str::startsWith($value, '/') && ! Str::startsWith($value, '//')) {
            return;
        }

        if (Str::isUrl($value, ['http', 'https'])) {
            return;
        }

        $fail('The :attribute must be a path that starts with / or a http(s) URL.');
    }
}
