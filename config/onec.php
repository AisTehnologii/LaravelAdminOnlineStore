<?php

return [
    'user' => env('ONEC_USER'),
    'pass' => env('ONEC_PASS'),

    'default_locale' => env('ONEC_DEFAULT_LOCALE', 'ru'),

    // max size for mode=file
    'file_limit' => (int) env('ONEC_FILE_LIMIT', 20_000_000),

    // storage paths
    'incoming_dir' => storage_path('app/private/1c/incoming'),
    'public_dir'   => storage_path('app/public/1c'),
];
