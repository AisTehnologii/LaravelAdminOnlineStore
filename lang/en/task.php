<?php

return [
    'resource' => [
        'model'  => 'Task',
        'plural' => 'Tasks',
    ],

    'page' => [
        'nav_group'     => 'Communication',
        'nav_label'     => 'Tasks',
        'badge_tooltip' => 'Active tasks',
    ],

    'pages' => [
        'view' => [
            'title'      => 'View task',
            'heading'    => 'View task',
            'breadcrumb' => 'View',
        ],
        'create' => [
            'title'      => 'Create task',
            'heading'    => 'Create task',
            'breadcrumb' => 'Create',
        ],
        'edit' => [
            'title'      => 'Edit task',
            'heading'    => 'Edit task',
            'breadcrumb' => 'Edit',
        ],
        'list' => [
            'title'      => 'Tasks',
            'heading'    => 'Tasks',
            'breadcrumb' => 'List',
        ],
    ],

    'form' => [
        'section'     => 'Task',
        'title'       => 'Title',
        'assignee'    => 'Assignee',
        'active'      => 'Active',
        'description' => 'Description',
        'attachments' => 'Attachments',
    ],

    'table' => [
        'active'   => 'Active',
        'title'    => 'Title',
        'assignee' => 'Assignee',
        'comments' => 'Comments',
        'updated'  => 'Updated',
        'dash'     => '—',
    ],

    'filters' => [
        'active' => 'Active',
    ],

    'actions' => [
        'view'            => 'View',
        'edit'            => 'Edit',
        'delete_selected' => 'Delete selected',
    ],

    'view' => [
        'section'     => 'Task',
        'title'       => 'Title',
        'assignee'    => 'Assignee',
        'active'      => 'Active',
        'description' => 'Description',
        'attachments' => 'Attachments',
    ],
];
