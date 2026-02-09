<?php

return [
    'navigation_label' => 'Backups',

    'page' => [
        'title' => '📦 Backups',
        'subtitle' => 'Aici sunt stocate backup-urile ZIP ale proiectului. Poți crea unul nou și descărca orice din listă.',
        'badges' => [
            'env' => '✅ .env inclus',
            'full' => '📦 Backup FULL (toate folderele)',
        ],
    ],

    'fields' => [
        'name'    => 'Backup',
        'size'    => 'Mărime',
        'by'      => 'De către',
        'created' => 'Creat la',
    ],

    'actions' => [
        'download' => 'Descarcă',
        'create_project' => 'Creează backup proiect',
    ],

    'modal' => [
        'create_heading' => 'Creează backup',
        'create_description' => 'Va fi generat un ZIP al proiectului.',
    ],

    'notifications' => [
        'no_rights' => [
            'title' => 'Nu ai permisiuni suficiente',
        ],
        'created' => [
            'title' => 'Backup creat',
        ],
    ],
];
