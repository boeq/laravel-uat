<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResolveFeedbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
