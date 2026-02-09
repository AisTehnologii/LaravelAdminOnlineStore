<?php

return [
    'page_title' => 'Panou informațional',

    'hero' => [
        'welcome' => 'Bine ai venit, :name 👋',
        'intro'   => 'Aceasta este panoul administrativ al site-ului. Aici gestionezi conținutul, utilizatorii, rolurile, comunicarea (chat) și sarcinile echipei.',
        'email'   => '✉️ :email',
        'role'    => '🛡 Rol: :role',
        'actions' => [
            'open_chat'  => '💬 Deschide chat-ul',
            'open_tasks' => '✅ Deschide sarcinile',
        ],
        'cards' => [
            'hint' => [
                'title' => 'Sugestie',
                'text'  => 'Începe cu :blocks — acolo sunt textele principale ale site-ului.',
            ],
            'team' => [
                'title' => 'Echipă',
                'text'  => 'Pentru întrebări — :chats, pentru sarcini — :tasks.',
            ],
            'permissions' => [
                'title' => 'Drepturi',
                'text'  => 'Rolurile și accesul se configurează în :roles (Shield).',
            ],
        ],
    ],

    'widgets' => [
        'title'    => 'Sumar',
        'subtitle' => 'Sarcini, necitite, online și acțiuni recente.',
        'live'     => '📌 Live',

        'common' => [
            'live' => 'Live',
            'time' => '🕒',
        ],

        'active_tasks' => [
            'title' => '📊 Sarcini active',
            'total' => '✅ :count',
            'mine' => '🧑‍💻 Ale mele: :count',
            'subtitle' => 'Ultimele sarcini active — deschide rapid și continuă lucrul.',
            'in_progress' => 'În lucru',
            'empty' => 'Nu există sarcini active.',
        ],

        'online_users' => [
            'title' => '👥 Utilizatori online',
            'count' => '🟢 :count',
            'window' => '⏱ :minutes min',
            'subtitle' => 'Cine este activ în panou — se actualizează după last_seen_at.',
            'empty' => 'Momentan nu se vede nimeni online.',
        ],

        'recent_actions' => [
            'title' => '🕒 Acțiuni recente',
            'badge' => '🧾 Revisions',
            'subtitle' => 'Cine și ce a modificat în sistem — util pentru control.',
            'history_arrow' => 'History →',
            'system' => 'System',
            'empty' => 'Istoricul este gol momentan.',
        ],

        'unread_chats' => [
            'title' => '💬 Chat-uri necitite',
            'count' => '🔔 :count',
            'subtitle' => 'Dialoguri cu mesaje noi — deschide și răspunde.',
            'dialog' => 'Dialog #:id',
            'unread' => 'unread',
            'reply' => 'Răspunde',
            'empty' => 'Nu există chat-uri necitite.',
        ],
    ],

    'quick' => [
        'title'    => 'Start rapid',
        'subtitle' => 'Apasă pe card — se va deschide instrucțiunea detaliată.',
        'cards' => [
            'blocks' => [
                'title' => '📝 Actualizează textele site-ului',
                'desc'  => 'Deschide :blocks și modifică textele (HTML este acceptat).',
                'help'  => [
                    'Alege <b>secțiunea</b> necesară (de ex.: <code>home.about_card</code>, <code>layout.footer</code>) și câmpul dorit.',
                    'Pe front textele se afișează ca HTML: <code>{!! $variable !!}</code>. Poți insera linkuri, liste, rânduri noi.',
                    'Dacă inserezi HTML — verifică să nu existe taguri neînchise.',
                    'După modificări, actualizează front-ul și verifică RU/RO/EN.',
                ],
            ],
            'chats' => [
                'title' => '💬 Comunicare în chat',
                'desc'  => 'Chat-uri personale și support. Atașamente, emoji, necitite, răspunsuri rapide.',
                'help'  => [
                    'Stânga: utilizatori și dialoguri. Dreapta: mesaje.',
                    '<b>Enter</b> — trimitere, <b>Shift+Enter</b> — rând nou.',
                    'Poți atașa fișiere/imagini — se salvează în storage și se descarcă prin rută protejată.',
                    'Necititele se calculează prin <code>last_read_at</code> al participantului.',
                ],
            ],
            'tasks' => [
                'title' => '✅ Sarcinile echipei',
                'desc'  => 'Creare sarcini, atribuirea responsabililor, comentarii și atașamente direct pe pagina de vizualizare.',
                'help'  => [
                    'În sarcină fixează: ce trebuie făcut, termen, responsabil, atașamente.',
                    'Pe pagina <b>View</b> adaugă comentarii — este istoricul lucrului la sarcină.',
                    'Atașamentele la comentarii ajută să nu pierzi fișierele în discuții.',
                ],
            ],
            'users_roles' => [
                'title' => '👤 Utilizatori și roluri',
                'desc'  => 'Gestionarea utilizatorilor, atribuirea rolurilor (Shield), SUPER_ADMIN prin <code>is_admin</code>.',
                'help'  => [
                    'SUPER_ADMIN (<code>is_admin=1</code>) trece orice verificare prin <code>Gate::before</code>.',
                    'Pentru ceilalți utilizatori, drepturile se acordă prin roluri/permissions (Shield).',
                    'Dacă după generarea drepturilor ceva „nu se vede” — ajută <code>php artisan permission:cache-reset</code> și <code>php artisan optimize:clear</code>.',
                ],
            ],
        ],
    ],

    'structure' => [
        'title'    => 'Unde se află ce',
        'subtitle' => 'Apasă pe card — se va deschide descrierea secțiunii.',
        'badge'    => '🧭 Navigație',

        'content' => [
            'title' => 'Content',
            'hint'  => 'Conținut, secțiuni, entități',

            'sections' => [
                'title' => '🧩 Content sections',
                'desc'  => 'Activare/dezactivare secțiuni și structura paginilor (legare prin section_id).',
                'help'  => [
                    'Secțiune = „comutator” al blocului pe pagină (câmpul <code>is_active</code>).',
                    'Entitățile (banners/sliders/cards/…) sunt legate prin <code>section_id</code> — important pentru ordinea afișării.',
                    'Dacă secțiunea e dezactivată — pe front blocul nu se afișează și înregistrările nu se încarcă.',
                ],
            ],

            'blocks' => [
                'title' => '📝 Content blocks',
                'desc'  => 'Textele site-ului (EN/RU/RO). Afișare pe front prin :raw.',
                'help'  => [
                    'Modifici textul — rezultatul se vede pe front după refresh.',
                    'Pentru rânduri noi folosește HTML: <code>&lt;br&gt;</code> sau liste.',
                    'Completează RU/RO/EN consecvent ca să nu rămână localizări „goale”.',
                ],
            ],

            'banners' => [
                'title' => '🖼 Banners',
                'desc'  => 'Hero-bannere și poziții. Butonul CTA se afișează o singură dată în afara caruselului.',
                'help'  => [
                    'Urmărește <code>position</code> — acesta este ordinea slide-urilor pe front.',
                    'Butonul CTA pe front trebuie afișat <b>o singură dată</b> (în afara owl-carousel).',
                    'După schimbarea imaginilor verifică adaptarea (mobile).',
                ],
            ],

            'sliders_cards' => [
                'title' => '🎞 Sliders / 🧱 Cards',
                'desc'  => 'Slidere și carduri de servicii. Localizare + poziții + secțiuni.',
                'help'  => [
                    'Fiecare înregistrare are <code>locale</code> — verifică să fie completate RU/RO/EN.',
                    'Pozițiile (<code>position</code>) e bine să fie consecutive pentru o ordine previzibilă.',
                    'Dacă un bloc dispare — verifică secțiunea (Content sections) și legătura <code>section_id</code>.',
                ],
            ],
        ],

        'access' => [
            'title' => 'Access',
            'hint'  => 'Istoric modificări',

            'history' => [
                'title' => '🕘 History',
                'desc'  => 'Istoric: create/update/delete, old/new values, rollback (doar SUPER_ADMIN).',
                'help'  => [
                    'Istoricul fixează evenimentele <code>created/updated/deleted</code> pentru modele.',
                    'Rollback este disponibil doar pentru SUPER_ADMIN.',
                    'Ca să înțelegi ce s-a schimbat — vezi <code>old_values</code> și <code>new_values</code>.',
                ],
            ],
        ],
    ],

    'common' => [
        'go'          => 'Mergi →',
        'open'        => 'Deschide →',
        'sections'    => 'Secțiuni →',
        'how_to'      => 'Cum să lucrezi corect',
        'instruction' => 'Instrucțiune',
        'tips'        => 'Sfaturi',
        'open_arrow'  => 'Deschide →',
        'dash'        => '—',
    ],
];
