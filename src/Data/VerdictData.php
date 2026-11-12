<?php

declare(strict_types=1);

namespace Boeq\Uat\Data;

use Boeq\Uat\Enums\Verdict;

/**
 * @property Verdict $verdict
 * @property ?string $feedback
 * @property string $page_url
 * @property ?string $user_agent
 * @property int $viewport_width
 * @property int $viewport_height
 *
 * @extends Data<string, mixed>
 */
class VerdictData extends Data
{
    //
}
