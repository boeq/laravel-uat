<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Api;

use Boeq\Uat\Enums\ScenarioStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(ScenarioStatus::class)],
        ];
    }
}
