@use('Boeq\Uat\Enums\Verdict')

<div class="flex flex-col gap-3 px-4 py-3">
    <h3 class="flex items-center gap-3 font-semibold">
        @include('uat::partials.verdict')

        {{ $step->title }}
    </h3>

    <p class="whitespace-pre-line">{{ $step->instruction }}</p>

    <div>
        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            {{ __('uat::bubble.expected_result') }}
        </h4>

        <p class="whitespace-pre-line">{{ $step->expected_result }}</p>
    </div>

    @if ($step->url)
        <p>
            <x-uat::link :url="$step->url" :label="$step->link_label ?? __('uat::bubble.open_page')" />
        </p>
    @endif

    <form method="POST" action="{{ route('uat.sessions.steps.update', [$session, $step]) }}">
        @csrf
        @method('PUT')

        <input type="hidden" name="verdict" value="{{ Verdict::Accepted }}">

        <button type="submit" class="w-full rounded-md px-3 py-2 font-medium {{ config()->string('uat.bubble.classes.accept') }}">
            {{ __('uat::bubble.accept') }}
        </button>
    </form>

    <details class="rounded-md border border-gray-200 dark:border-gray-700" @if ($step->verdict === Verdict::Feedback) open @endif>
        <summary class="cursor-pointer px-3 py-2 font-medium">{{ __('uat::bubble.feedback') }}</summary>

        <form
            method="POST"
            action="{{ route('uat.sessions.steps.update', [$session, $step]) }}"
            class="flex flex-col gap-2 px-3 pb-3"
        >
            @csrf
            @method('PUT')

            <input type="hidden" name="verdict" value="{{ Verdict::Feedback }}">

            <label for="uat-feedback" class="font-medium">{{ __('uat::bubble.feedback_label') }}</label>

            <textarea
                id="uat-feedback"
                name="feedback"
                rows="4"
                required
                minlength="{{ config()->integer('uat.feedback.min_length') }}"
                aria-describedby="uat-feedback-hint"
                class="w-full rounded-md border border-gray-300 bg-white p-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            >{{ $step->feedback }}</textarea>

            <p id="uat-feedback-hint" class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('uat::bubble.feedback_hint') }}
            </p>

            <button type="submit" class="rounded-md px-3 py-2 font-medium {{ config()->string('uat.bubble.classes.feedback') }}">
                {{ __('uat::bubble.send_feedback') }}
            </button>
        </form>
    </details>
</div>
