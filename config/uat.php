<?php

declare(strict_types=1);

return [
    /* Turns the bubble off for an environment, whatever the TestsFeatures contract returns */
    'enabled' => env('UAT_ENABLED', true),

    /* Languages scenarios can be written in, the API rejects any other language */
    'locales' => ['en'],

    /* Shown when a scenario has no translation for the current locale */
    'default_locale' => 'en',

    'table_prefix' => 'uat_',

    'feedback' => [
        /* Feedback shorter than this is rejected, so "no" or "bug" is not enough */
        'min_length' => 10,
    ],

    'bubble' => [
        'prefix' => 'uat',

        'middleware' => ['web'],

        /* bottom-right, bottom-left, top-right or top-left */
        'position' => 'bottom-right',

        /* Links to pages of this application open in the same tab, external links always open in a new tab */
        'links_in_new_tab' => false,

        /* The scenario list gets a search field once it holds more scenarios than this */
        'search_threshold' => 8,

        /* Tailwind classes, add this file to the sources of your Tailwind build when you change them */
        'classes' => [
            'container' => 'z-50',
            'button' => 'bg-indigo-600 text-white hover:bg-indigo-500',
            'badge' => 'bg-red-600 text-white',
            'accept' => 'bg-green-600 text-white hover:bg-green-500',
            'feedback' => 'bg-amber-500 text-white hover:bg-amber-400',
        ],
    ],

    'api' => [
        'enabled' => env('UAT_API_ENABLED', true),

        'prefix' => 'api/uat',

        'middleware' => ['api'],
    ],
];
