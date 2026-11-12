<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Bubble;

use Illuminate\Foundation\Http\FormRequest;

class CompleteSessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'closing_comment' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
