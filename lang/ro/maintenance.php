<?php

return [
    'page' => [
        'nav_group' => 'Acces',
    'nav_label' => 'Mentenanță',
        'title'       => 'Mod de mentenanță',
        'heading'     => 'Mod de mentenanță',
        'subheading'  => 'Activare/dezactivare site, mesaj pentru clienți și whitelist IP.',
        'breadcrumb'  => 'Mentenanță',
    ],

    'form' => [
        'enabled'        => 'Activează modul de mentenanță',
        'message'        => 'Mesaj',
        'message_help'   => 'Acest text va fi afișat utilizatorilor pe pagina de mentenanță.',
        'until'          => 'Dezactivare automată (până la)',
        'until_help'     => 'Dacă este setat — modul se va opri automat după această dată/oră.',
        'allow_ips'      => 'Whitelist IP',
        'allow_ips_help' => 'IP-urile din listă pot accesa site-ul chiar dacă modul este activ.',
    ],

    'actions' => [
        'save' => 'Salvează',
    ],

    'notifications' => [
        'saved_title' => 'Salvat',
        'saved_body'  => 'Setările de mentenanță au fost actualizate.',
    ],

    'public' => [
        'title'           => 'Mentenanță',
        'badge'           => 'Mod de mentenanță',
        'heading'         => 'Site-ul este temporar indisponibil',
        'default_message' => 'Efectuăm lucrări tehnice. Vă rugăm să reveniți mai târziu.',
    ],
];
