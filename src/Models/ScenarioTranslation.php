<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Database\Factories\ScenarioTranslationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $scenario_id
 * @property string $locale
 * @property string $title
 * @property string $description
 * @property ?string $preconditions
 * @property ?string $start_url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[UseFactory(ScenarioTranslationFactory::class)]
class ScenarioTranslation extends Model
{
    /** @use HasFactory<ScenarioTranslationFactory> */
    use HasFactory;
}
