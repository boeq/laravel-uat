<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel UAT demo</title>

        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <nav class="flex items-center gap-6 border-b border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
            <a href="/orders" class="font-semibold">Laravel UAT demo</a>

            <span class="ml-auto text-sm">
                @auth
                    {{ auth()->user()->name }} &middot; <a href="/_workbench/logout" class="underline">Log out</a>
                @else
                    <a href="/_workbench/login/1" class="underline">Log in as the demo tester</a>
                @endauth
            </span>
        </nav>

        <main class="mx-auto max-w-3xl px-6 py-10">
            @yield('content')
        </main>

        <x-uat::bubble />
    </body>
</html>
