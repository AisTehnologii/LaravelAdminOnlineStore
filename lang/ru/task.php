<?php

return [
    'resource' => [
        'model'  => 'Задача',
        'plural' => 'Задачи',
    ],

    'page' => [
        'nav_group'     => 'Коммуникации',
        'nav_label'     => 'Задачи',
        'badge_tooltip' => 'Активные задачи',
    ],

    'pages' => [
        'view' => [
            'title'      => 'Просмотр задачи',
            'heading'    => 'Просмотр задачи',
            'breadcrumb' => 'Просмотр',
        ],
        'create' => [
            'title'      => 'Создать задачу',
            'heading'    => 'Создать задачу',
            'breadcrumb' => 'Создать',
        ],
        'edit' => [
            'title'      => 'Редактирование задачи',
            'heading'    => 'Редактирование задачи',
            'breadcrumb' => 'Редактировать',
        ],
        'list' => [
            'title'      => 'Задачи',
            'heading'    => 'Задачи',
            'breadcrumb' => 'Список',
        ],
    ],

    'form' => [
        'section'     => 'Задача',
        'title'       => 'Название',
        'assignee'    => 'Ответственный',
        'active'      => 'Активна',
        'description' => 'Описание',
        'attachments' => 'Вложения',
    ],

    'table' => [
        'active'   => 'Активна',
        'title'    => 'Название',
        'assignee' => 'Ответственный',
        'comments' => 'Комментарии',
        'updated'  => 'Обновлено',
        'dash'     => '—',
    ],

    'filters' => [
        'active' => 'Активна',
    ],

    'actions' => [
        'view'            => 'Просмотр',
        'edit'            => 'Редактировать',
        'delete_selected' => 'Удалить выбранные',
    ],

    'view' => [
        'section'     => 'Задача',
        'title'       => 'Название',
        'assignee'    => 'Ответственный',
        'active'      => 'Активна',
        'description' => 'Описание',
        'attachments' => 'Вложения',
    ],
];
