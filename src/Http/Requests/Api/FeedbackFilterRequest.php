<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Api;

use Boeq\Uat\Enums\FeedbackStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedbackFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [Rule::enum(FeedbackStatus::class)],
            'scenario' => ['integer'],
        ];
    }
}
