<?php

return [
    'page' => [
        'nav_group' => 'Access',
    'nav_label' => 'Maintenance',
        'title'       => 'Maintenance Mode',
        'heading'     => 'Maintenance Mode',
        'subheading'  => 'Enable/disable the site, set a user message, and manage IP whitelist.',
        'breadcrumb'  => 'Maintenance',
    ],

    'form' => [
        'enabled'        => 'Enable maintenance mode',
        'message'        => 'Message',
        'message_help'   => 'This text will be shown to users on the maintenance page.',
        'until'          => 'Auto-disable (until)',
        'until_help'     => 'If set, the mode will turn off automatically after this date/time.',
        'allow_ips'      => 'IP whitelist',
        'allow_ips_help' => 'Listed IPs can access the site even when maintenance is enabled.',
    ],

    'actions' => [
        'save' => 'Save',
    ],

    'notifications' => [
        'saved_title' => 'Saved',
        'saved_body'  => 'Maintenance settings have been updated.',
    ],

    'public' => [
        'title'           => 'Maintenance',
        'badge'           => 'Maintenance mode',
        'heading'         => 'The site is temporarily unavailable',
        'default_message' => 'We are performing maintenance. Please try again later.',
    ],
];
