@php($translation = $progress->scenario->translation(app()->getLocale()))

<li data-uat-scenario class="border-b border-gray-100 dark:border-gray-800">
    <form method="POST" action="{{ route('uat.sessions.store', $progress->scenario) }}">
        @csrf

        <button type="submit" class="block w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-800">
            <span class="block font-medium">{{ $translation->title }}</span>

            <span class="block truncate text-gray-600 dark:text-gray-400">{{ $translation->description }}</span>

            <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">
                {{ trans_choice('uat::bubble.steps', $progress->scenario->steps_count) }}
                &middot;
                @include('uat::partials.status')
            </span>
        </button>
    </form>
</li>
