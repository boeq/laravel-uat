@use('Boeq\Uat\Enums\Verdict')

<x-uat::frame>
    @php($judged = $session->steps->whereNotNull('verdict'))
    @php($translation = $session->scenario->translation($session->locale))
    @php($startUrl = $translation->start_url ?? $session->scenario->start_url)

    <header class="flex flex-col gap-2 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
        <form method="GET" action="{{ route('uat.scenarios.index') }}">
            <button type="submit" class="text-xs font-medium text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                &larr; {{ __('uat::bubble.back') }}
            </button>
        </form>

        <h2 class="text-base font-semibold">{{ $translation->title }}</h2>

        <div
            role="progressbar"
            aria-label="{{ $judged->count() }}/{{ $session->steps->count() }}"
            aria-valuemin="0"
            aria-valuemax="{{ $session->steps->count() }}"
            aria-valuenow="{{ $judged->count() }}"
            class="h-2 w-full overflow-hidden rounded-full bg-gray-200 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400"
        >
            {{-- The width comes from the viewBox, so the bar needs no inline style and works under a strict CSP. --}}
            <svg class="h-full w-full" viewBox="0 0 {{ $session->steps->count() }} 1" preserveAspectRatio="none" fill="currentColor" aria-hidden="true">
                <rect width="{{ $judged->count() }}" height="1" />
            </svg>
        </div>
    </header>

    <details class="border-b border-gray-200 px-4 py-3 dark:border-gray-700" @if ($judged->isEmpty()) open @endif>
        <summary class="cursor-pointer font-medium">{{ __('uat::bubble.about') }}</summary>

        <div class="mt-3 flex flex-col gap-3">
            @if ($session->locale !== app()->getLocale())
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('uat::bubble.shown_in', ['locale' => strtoupper($session->locale)]) }}
                </p>
            @endif

            <p class="whitespace-pre-line">{{ $translation->description }}</p>

            @if ($translation->preconditions)
                <div class="rounded-md bg-gray-50 p-3 dark:bg-gray-800">
                    <h3 class="font-medium">{{ __('uat::bubble.preconditions') }}</h3>

                    <p class="whitespace-pre-line">{{ $translation->preconditions }}</p>
                </div>
            @endif

            @if ($startUrl)
                <p>
                    <x-uat::link :url="$startUrl" :label="__('uat::bubble.open_page')" />
                </p>
            @endif
        </div>
    </details>

    <ol>
        @foreach ($session->steps as $step)
            <li class="border-b border-gray-100 dark:border-gray-800">
                @if ($step->is($openStep))
                    @include('uat::partials.step')
                @else
                    <form method="GET" action="{{ route('uat.sessions.show', $session) }}">
                        <input type="hidden" name="step" value="{{ $step->id }}">

                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-800">
                            @include('uat::partials.verdict')

                            {{ $step->title }}
                        </button>
                    </form>
                @endif
            </li>
        @endforeach
    </ol>

    @if ($judged->count() === $session->steps->count())
        <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-800">
            <p class="font-medium">
                {{ __('uat::bubble.summary', [
                    'accepted' => $judged->where('verdict', Verdict::Accepted)->count(),
                    'feedback' => $judged->where('verdict', Verdict::Feedback)->count(),
                ]) }}
            </p>

            <form method="POST" action="{{ route('uat.sessions.complete', $session) }}" class="flex flex-col gap-2">
                @csrf

                <label for="uat-closing-comment" class="font-medium">{{ __('uat::bubble.closing_comment') }}</label>

                <textarea
                    id="uat-closing-comment"
                    name="closing_comment"
                    rows="3"
                    class="w-full rounded-md border border-gray-300 bg-white p-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                ></textarea>

                <button type="submit" class="rounded-md px-3 py-2 font-medium {{ config()->string('uat.bubble.classes.button') }}">
                    {{ __('uat::bubble.finish') }}
                </button>
            </form>
        </div>
    @endif

    <footer class="flex items-center justify-between gap-3 px-4 py-3">
        <form method="POST" action="{{ route('uat.sessions.abandon', $session) }}">
            @csrf

            <button type="submit" class="text-xs font-medium text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                {{ __('uat::bubble.abandon') }}
            </button>
        </form>

        @if ($judged->count() < $session->steps->count())
            <form method="POST" action="{{ route('uat.sessions.accept', $session) }}">
                @csrf

                <button type="submit" class="rounded-md px-3 py-2 font-medium {{ config()->string('uat.bubble.classes.accept') }}">
                    {{ __('uat::bubble.accept_all') }}
                </button>
            </form>
        @endif
    </footer>
</x-uat::frame>
