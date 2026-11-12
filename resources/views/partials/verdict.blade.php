@use('Boeq\Uat\Enums\Verdict')

<span
    @class([
        'inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold',
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $step->verdict === Verdict::Accepted,
        'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200' => $step->verdict === Verdict::Feedback,
        'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => $step->verdict === null,
    ])
>
    @if ($step->verdict === Verdict::Accepted)
        <span aria-hidden="true">&check;</span>
        <span class="sr-only">{{ __('uat::bubble.accepted') }}</span>
    @elseif ($step->verdict === Verdict::Feedback)
        <span aria-hidden="true">!</span>
        <span class="sr-only">{{ __('uat::bubble.feedback') }}</span>
    @else
        {{ $step->position }}
    @endif
</span>
