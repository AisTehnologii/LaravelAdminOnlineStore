<?php

return [
    'navigation_label' => 'Users',
    'model_label'      => 'User',
    'plural_label'     => 'Users',

    'sections' => [
        'user'     => 'User',
        'security' => 'Security',
        'role'     => 'Role',
    ],

    'fields' => [
        'name'                  => 'Name',
        'email'                 => 'Email',
        'is_admin'              => 'Super admin (full access)',
        'password'              => 'Password',
        'password_confirmation' => 'Confirm password',
        'role'                  => 'Role',
    ],

    'helpers' => [
        'is_admin' => 'Full access to the admin panel, regardless of roles.',
        'password' => 'Leave empty if you don’t want to change the password.',
        'role'     => 'Assign a role to the user. For is_admin you can keep admin.',
    ],

    'table' => [
        'id'    => 'ID',
        'name'  => 'Name',
        'email' => 'Email',
        'super' => 'Super',
        'role'  => 'Role',
    ],

    'actions' => [
        'edit'            => 'Edit',
        'delete'          => 'Delete',
        'delete_selected' => 'Delete selected',
    ],

    'common' => [
        'dash' => '—',
    ],
];
