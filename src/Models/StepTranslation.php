<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Boeq\Uat\Database\Factories\StepTranslationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $step_id
 * @property string $locale
 * @property string $title
 * @property string $instruction
 * @property string $expected_result
 * @property ?string $link_label
 * @property ?string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[UseFactory(StepTranslationFactory::class)]
class StepTranslation extends Model
{
    /** @use HasFactory<StepTranslationFactory> */
    use HasFactory;
}
