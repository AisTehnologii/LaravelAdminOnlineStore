<?php

return [
    'navigation_label' => 'Бэкапы',

    'page' => [
        'title' => '📦 Бэкапы',
        'subtitle' => 'Здесь хранятся ZIP-бэкапы проекта. Можно создать новый и скачать любой из списка.',
        'badges' => [
            'env' => '✅ .env включается',
            'full' => '📦 FULL backup (все папки)',
        ],
    ],

    'fields' => [
        'name'    => 'Бэкап',
        'size'    => 'Размер',
        'by'      => 'Кем',
        'created' => 'Создано',
    ],

    'actions' => [
        'download' => 'Скачать',
        'create_project' => 'Создать бэкап проекта',
    ],

    'modal' => [
        'create_heading' => 'Создать бэкап',
        'create_description' => 'Будет сформирован ZIP проекта.',
    ],

    'notifications' => [
        'no_rights' => [
            'title' => 'Недостаточно прав',
        ],
        'created' => [
            'title' => 'Бэкап создан',
        ],
    ],
];
