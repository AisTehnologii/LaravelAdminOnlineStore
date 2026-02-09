<?php

return [
    'navigation_label' => 'Utilizatori',
    'model_label'      => 'Utilizator',
    'plural_label'     => 'Utilizatori',

    'sections' => [
        'user'     => 'Utilizator',
        'security' => 'Securitate',
        'role'     => 'Rol',
    ],

    'fields' => [
        'name'                  => 'Nume',
        'email'                 => 'Email',
        'is_admin'              => 'Super admin (acces complet)',
        'password'              => 'Parolă',
        'password_confirmation' => 'Confirmă parola',
        'role'                  => 'Rol',
    ],

    'helpers' => [
        'is_admin' => 'Acces complet în panou, indiferent de roluri.',
        'password' => 'Lasă gol dacă nu vrei să schimbi parola.',
        'role'     => 'Alege rolul utilizatorului. Pentru is_admin poți lăsa admin.',
    ],

    'table' => [
        'id'    => 'ID',
        'name'  => 'Nume',
        'email' => 'Email',
        'super' => 'Super',
        'role'  => 'Rol',
    ],

    'actions' => [
        'edit'            => 'Editează',
        'delete'          => 'Șterge',
        'delete_selected' => 'Șterge selectate',
    ],

    'common' => [
        'dash' => '—',
    ],
];
