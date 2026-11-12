<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Bubble;

use Boeq\Uat\Enums\Verdict;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerdictRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'verdict' => ['required', Rule::enum(Verdict::class)],
            'feedback' => ['nullable', 'required_if:verdict,'.Verdict::Feedback->value, 'string', 'min:'.config()->integer('uat.feedback.min_length')],
            'page_url' => ['required', 'string', 'max:2048'],
            'viewport_width' => ['required', 'integer', 'min:0'],
            'viewport_height' => ['required', 'integer', 'min:0'],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        return [
            'feedback.required_if' => __('uat::bubble.feedback_hint'),
            'feedback.min' => __('uat::bubble.feedback_hint'),
        ];
    }
}
