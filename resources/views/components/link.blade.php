@props(['url', 'label'])

<a
    href="{{ $url }}"
    @if (! str_starts_with($url, '/') || config()->boolean('uat.bubble.links_in_new_tab'))
        target="_blank"
        rel="noopener noreferrer"
    @endif
    class="font-medium text-indigo-600 underline hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
>{{ $label }}</a>
