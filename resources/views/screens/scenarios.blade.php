<x-uat::frame>
    @php($open = $scenarios->reject->isFinished())
    @php($completed = $scenarios->filter->isFinished())

    <header class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
        <h2 class="text-base font-semibold">{{ __('uat::bubble.scenarios') }}</h2>
    </header>

    @if ($open->isEmpty())
        <p class="border-b border-gray-100 px-4 py-3 text-gray-600 dark:border-gray-800 dark:text-gray-400">
            {{ __('uat::bubble.empty') }}
        </p>
    @endif

    @if ($open->count() > config()->integer('uat.bubble.search_threshold'))
        <div class="border-b border-gray-200 px-4 py-2 dark:border-gray-700">
            <input
                type="search"
                data-uat-search
                placeholder="{{ __('uat::bubble.search') }}"
                aria-label="{{ __('uat::bubble.search') }}"
                class="w-full rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            >
        </div>
    @endif

    <ul>
        @foreach ($open as $progress)
            @include('uat::partials.scenario')
        @endforeach
    </ul>

    @if ($completed->isNotEmpty())
        <details>
            <summary class="cursor-pointer list-none px-4 py-3 text-xs font-medium text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 [&::-webkit-details-marker]:hidden">
                {{ __('uat::bubble.completed_scenarios', ['count' => $completed->count()]) }}
            </summary>

            <ul class="border-t border-gray-100 dark:border-gray-800">
                @foreach ($completed as $progress)
                    @include('uat::partials.scenario')
                @endforeach
            </ul>
        </details>
    @endif
</x-uat::frame>
