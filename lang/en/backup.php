<?php

return [
    'navigation_label' => 'Backups',

    'page' => [
        'title' => '📦 Backups',
        'subtitle' => 'ZIP backups of the project are stored here. You can create a new one and download any from the list.',
        'badges' => [
            'env' => '✅ .env included',
            'full' => '📦 FULL backup (all folders)',
        ],
    ],

    'fields' => [
        'name'    => 'Backup',
        'size'    => 'Size',
        'by'      => 'By',
        'created' => 'Created',
    ],

    'actions' => [
        'download' => 'Download',
        'create_project' => 'Create project backup',
    ],

    'modal' => [
        'create_heading' => 'Create backup',
        'create_description' => 'A ZIP archive of the project will be generated.',
    ],

    'notifications' => [
        'no_rights' => [
            'title' => 'Not enough permissions',
        ],
        'created' => [
            'title' => 'Backup created',
        ],
    ],
];
