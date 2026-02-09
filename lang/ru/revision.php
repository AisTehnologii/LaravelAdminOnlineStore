<?php

return [
    'navigation_label' => 'История',

    'common' => [
        'dash' => '—',
    ],

    'form' => [
        'old_values' => 'Старые значения',
        'new_values' => 'Новые значения',
    ],

    'table' => [
        'date'       => 'Дата',
        'event'      => 'Событие',
        'model'      => 'Модель',
        'id'         => 'ID',
        'user'       => 'Пользователь',
        'ip'         => 'IP',
        'user_agent' => 'User agent',
    ],

    'filters' => [
        'event' => 'Событие',
        'model' => 'Модель',
    ],

    'events' => [
        'created' => 'создано',
        'updated' => 'обновлено',
        'deleted' => 'удалено',
    ],

    'actions' => [
        'view'          => 'Просмотр',
        'rollback'      => 'Откат',
        'clear_history' => 'Очистить историю',
    ],

    'modal' => [
        'clear_heading'     => 'Очистить историю изменений',
        'clear_description' => 'Это действие удалит ВСЮ историю изменений без возможности восстановления.',
        'clear_submit'      => 'Да, очистить',
    ],

    'notifications' => [
        'cleared'       => 'История изменений очищена',
        'rollback_done' => 'Откат выполнен',
    ],

    // для кастомного view.blade
    'view_page' => [
        'info_title' => 'Информация',
        'changes'    => 'Изменения',
        'field'      => 'Поле',
        'was'        => 'Было',
        'became'     => 'Стало',
        'date'       => 'Дата',
        'event'      => 'Событие',
        'model'      => 'Модель',
        'user'       => 'Пользователь',
        'ip'         => 'IP',
    ],
];
