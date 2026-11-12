<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Api;

use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Rules\Link;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TranslationRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var Scenario $scenario */
        $scenario = $this->route('scenario');

        return [
            'locale' => [Rule::in(config()->array('uat.locales'))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'preconditions' => ['nullable', 'string'],
            'start_url' => ['nullable', 'string', 'max:2048', new Link],
            'steps' => ['present', 'array'],
            'steps.*.id' => ['required', 'integer', 'distinct', Rule::exists(Step::class, 'id')->where('scenario_id', $scenario->id)],
            'steps.*.title' => ['required', 'string', 'max:255'],
            'steps.*.instruction' => ['required', 'string'],
            'steps.*.expected_result' => ['required', 'string'],
            'steps.*.link_label' => ['nullable', 'string', 'max:255'],
            'steps.*.url' => ['nullable', 'string', 'max:2048', new Link],
        ];
    }

    #[\Override]
    public function validationData(): array
    {
        return [...$this->all(), 'locale' => $this->route('locale')];
    }

    #[\Override]
    public function messages(): array
    {
        return [
            'locale.in' => 'Translations may only use these languages: '.implode(', ', config()->array('uat.locales')).'.',
            'steps.*.id.exists' => 'Step :position does not belong to this scenario.',
            'steps.*.title.required' => 'Step :position is missing :attribute.',
            'steps.*.instruction.required' => 'Step :position is missing :attribute.',
            'steps.*.expected_result.required' => 'Step :position is missing :attribute.',
        ];
    }

    #[\Override]
    public function attributes(): array
    {
        return [
            'steps.*.title' => 'title',
            'steps.*.instruction' => 'instruction',
            'steps.*.expected_result' => 'expected_result',
        ];
    }
}
