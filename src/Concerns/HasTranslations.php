<?php

declare(strict_types=1);

namespace Boeq\Uat\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @template TTranslation of Model
 */
trait HasTranslations
{
    /** @return HasMany<TTranslation, $this> */
    abstract public function translations(): HasMany;

    /** @return TTranslation */
    public function translation(string $locale): Model
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->sole('locale', config()->string('uat.default_locale'));
    }
}
