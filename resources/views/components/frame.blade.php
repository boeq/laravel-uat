<section
    data-uat-panel
    hidden
    aria-label="{{ __('uat::bubble.title') }}"
    class="max-h-[75vh] w-96 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-lg border border-gray-200 bg-white text-left text-sm text-gray-900 shadow-xl dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
>
    <p
        data-uat-message
        hidden
        role="alert"
        class="border-b border-red-200 bg-red-50 px-4 py-2 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200"
    ></p>

    {{ $slot }}
</section>

<div class="flex items-center gap-2">
    <button
        type="button"
        data-uat-hide
        title="{{ __('uat::bubble.hide') }}"
        aria-label="{{ __('uat::bubble.hide') }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-lg hover:text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
    >
        @include('uat::partials.icons.close')
    </button>

    <button
        type="button"
        data-uat-toggle
        aria-expanded="false"
        class="relative inline-flex h-12 items-center gap-2 rounded-full px-5 text-sm font-semibold shadow-lg {{ config()->string('uat.bubble.classes.button') }}"
    >
        @include('uat::partials.icons.checklist')

        @if ($session)
            {{ __('uat::bubble.step_of', [
                'step' => $session->currentStep()?->position ?? $session->steps->count(),
                'steps' => $session->steps->count(),
            ]) }}
        @else
            {{ __('uat::bubble.title') }}

            @if ($unfinished > 0)
                <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1 text-xs font-semibold {{ config()->string('uat.bubble.classes.badge') }}">
                    {{ $unfinished }}
                </span>
            @endif
        @endif
    </button>
</div>
