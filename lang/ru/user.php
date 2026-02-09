<?php

return [
    'navigation_label' => 'Пользователи',
    'model_label'      => 'Пользователь',
    'plural_label'     => 'Пользователи',

    'sections' => [
        'user'     => 'Пользователь',
        'security' => 'Безопасность',
        'role'     => 'Роль',
    ],

    'fields' => [
        'name'                  => 'Имя',
        'email'                 => 'Email',
        'is_admin'              => 'Супер-админ (полный доступ)',
        'password'              => 'Пароль',
        'password_confirmation' => 'Подтверждение пароля',
        'role'                  => 'Роль',
    ],

    'helpers' => [
        'is_admin' => 'Полный доступ ко всей админке, независимо от ролей.',
        'password' => 'Оставь пустым, если не нужно менять пароль.',
        'role'     => 'Назначь роль пользователю. Для is_admin можно оставить admin.',
    ],

    'table' => [
        'id'    => 'ID',
        'name'  => 'Имя',
        'email' => 'Email',
        'super' => 'Super',
        'role'  => 'Роль',
    ],

    'actions' => [
        'edit'            => 'Редактировать',
        'delete'          => 'Удалить',
        'delete_selected' => 'Удалить выбранные',
    ],

    'common' => [
        'dash' => '—',
    ],
];
