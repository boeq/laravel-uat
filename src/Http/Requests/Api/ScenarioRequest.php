<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Requests\Api;

use Boeq\Uat\Enums\ScenarioStatus;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Step;
use Boeq\Uat\Rules\Link;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScenarioRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var ?Scenario $scenario */
        $scenario = $this->route('scenario');

        $locales = implode(',', config()->array('uat.locales'));
        $defaultLocale = config()->string('uat.default_locale');
        $active = $scenario?->status === ScenarioStatus::Active;

        return [
            'start_url' => ['nullable', 'string', 'max:2048', new Link],
            'translations' => ['present', 'array:'.$locales],
            'translations.'.$defaultLocale => [Rule::requiredIf($active), 'array'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
            'translations.*.preconditions' => ['nullable', 'string'],
            'translations.*.start_url' => ['nullable', 'string', 'max:2048', new Link],
            'steps' => [Rule::requiredIf($active), 'present', 'array'],
            'steps.*.id' => $scenario instanceof Scenario
                ? ['integer', 'distinct', Rule::exists(Step::class, 'id')->where('scenario_id', $scenario->id)]
                : ['prohibited'],
            'steps.*.url' => ['nullable', 'string', 'max:2048', new Link],
            'steps.*.translations' => ['present', 'array:'.$locales],
            'steps.*.translations.'.$defaultLocale => [Rule::requiredIf($active), 'array'],
            'steps.*.translations.*.title' => ['required', 'string', 'max:255'],
            'steps.*.translations.*.instruction' => ['required', 'string'],
            'steps.*.translations.*.expected_result' => ['required', 'string'],
            'steps.*.translations.*.link_label' => ['nullable', 'string', 'max:255'],
            'steps.*.translations.*.url' => ['nullable', 'string', 'max:2048', new Link],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        $locales = implode(', ', config()->array('uat.locales'));
        $defaultLocale = config()->string('uat.default_locale');

        return [
            'translations.array' => 'Translations may only use these languages: '.$locales.'.',
            'translations.'.$defaultLocale.'.required' => 'An active scenario needs its '.$defaultLocale.' translation.',
            'translations.*.title.required' => 'The scenario is missing :attribute.',
            'translations.*.description.required' => 'The scenario is missing :attribute.',
            'steps.required' => 'An active scenario needs at least one step.',
            'steps.*.translations.array' => 'Step :position may only use these languages: '.$locales.'.',
            'steps.*.translations.'.$defaultLocale.'.required' => 'Step :position of an active scenario needs its '.$defaultLocale.' translation.',
            'steps.*.translations.*.title.required' => 'Step :position is missing :attribute.',
            'steps.*.translations.*.instruction.required' => 'Step :position is missing :attribute.',
            'steps.*.translations.*.expected_result.required' => 'Step :position is missing :attribute.',
        ];
    }

    #[\Override]
    public function attributes(): array
    {
        return collect(config()->array('uat.locales'))
            ->flatMap(fn (string $locale): array => [
                'translations.'.$locale.'.title' => $locale.'.title',
                'translations.'.$locale.'.description' => $locale.'.description',
                'steps.*.translations.'.$locale.'.title' => $locale.'.title',
                'steps.*.translations.'.$locale.'.instruction' => $locale.'.instruction',
                'steps.*.translations.'.$locale.'.expected_result' => $locale.'.expected_result',
            ])
            ->all();
    }
}
