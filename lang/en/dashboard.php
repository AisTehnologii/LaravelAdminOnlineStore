<?php

return [
    'page_title' => 'Information panel',

    'hero' => [
        'welcome' => 'Welcome, :name 👋',
        'intro'   => 'This is the site admin panel. Here you manage content, users, roles, communication (chat), and team tasks.',
        'email'   => '✉️ :email',
        'role'    => '🛡 Role: :role',
        'actions' => [
            'open_chat'  => '💬 Open chat',
            'open_tasks' => '✅ Open tasks',
        ],
        'cards' => [
            'hint' => [
                'title' => 'Hint',
                'text'  => 'Start with :blocks — the main website texts are there.',
            ],
            'team' => [
                'title' => 'Team',
                'text'  => 'For questions — :chats, for tasks — :tasks.',
            ],
            'permissions' => [
                'title' => 'Permissions',
                'text'  => 'Roles and access are configured in :roles (Shield).',
            ],
        ],
    ],

    'widgets' => [
        'title'    => 'Summary',
        'subtitle' => 'Tasks, unread, online, and recent actions.',
        'live'     => '📌 Live',

        'common' => [
            'live' => 'Live',
            'time' => '🕒',
        ],

        'active_tasks' => [
            'title' => '📊 Active tasks',
            'total' => '✅ :count',
            'mine' => '🧑‍💻 Mine: :count',
            'subtitle' => 'Latest active tasks — open quickly and continue working.',
            'in_progress' => 'In progress',
            'empty' => 'No active tasks.',
        ],

        'online_users' => [
            'title' => '👥 Online users',
            'count' => '🟢 :count',
            'window' => '⏱ :minutes min',
            'subtitle' => 'Who is active in the panel — updated via last_seen_at.',
            'empty' => 'No one is currently visible online.',
        ],

        'recent_actions' => [
            'title' => '🕒 Recent actions',
            'badge' => '🧾 Revisions',
            'subtitle' => 'Who changed what in the system — useful for control.',
            'history_arrow' => 'History →',
            'system' => 'System',
            'empty' => 'History is empty for now.',
        ],

        'unread_chats' => [
            'title' => '💬 Unread chats',
            'count' => '🔔 :count',
            'subtitle' => 'Conversations with new messages — open and reply.',
            'dialog' => 'Dialog #:id',
            'unread' => 'unread',
            'reply' => 'Reply',
            'empty' => 'No unread chats.',
        ],
    ],

    'quick' => [
        'title'    => 'Quick start',
        'subtitle' => 'Click a card — it will expand with a detailed instruction.',
        'cards' => [
            'blocks' => [
                'title' => '📝 Update website texts',
                'desc'  => 'Open :blocks and edit texts (HTML is supported).',
                'help'  => [
                    'Choose the required <b>section</b> (e.g. <code>home.about_card</code>, <code>layout.footer</code>) and the field you need.',
                    'On the front-end, texts are rendered as HTML: <code>{!! $variable !!}</code>. You can insert links, lists, line breaks.',
                    'If you insert HTML — make sure there are no unclosed tags.',
                    'After edits, refresh the front-end and check RU/RO/EN.',
                ],
            ],
            'chats' => [
                'title' => '💬 Chat communication',
                'desc'  => 'Personal and support chats. Attachments, emojis, unread, quick replies.',
                'help'  => [
                    'Left: users and dialogs. Right: messages.',
                    '<b>Enter</b> — send, <b>Shift+Enter</b> — new line.',
                    'You can attach files/images — they are stored in storage and downloaded via a protected route.',
                    'Unread is calculated by the participant’s <code>last_read_at</code> field.',
                ],
            ],
            'tasks' => [
                'title' => '✅ Team tasks',
                'desc'  => 'Create tasks, assign owners, comments and attachments right on the view page.',
                'help'  => [
                    'In a task, record: what to do, deadline, assignee, attachments.',
                    'On the <b>View</b> page, add comments — it’s the task work history.',
                    'Comment attachments help you not to lose files in conversations.',
                ],
            ],
            'users_roles' => [
                'title' => '👤 Users and roles',
                'desc'  => 'Manage users, assign roles (Shield), SUPER_ADMIN via <code>is_admin</code>.',
                'help'  => [
                    'SUPER_ADMIN (<code>is_admin=1</code>) passes any checks via <code>Gate::before</code>.',
                    'For other users, permissions are granted via roles/permissions (Shield).',
                    'If something is “not visible” after generating permissions — try <code>php artisan permission:cache-reset</code> and <code>php artisan optimize:clear</code>.',
                ],
            ],
        ],
    ],

    'structure' => [
        'title'    => 'Where things are',
        'subtitle' => 'Click a card — it will expand with a section description.',
        'badge'    => '🧭 Navigation',

        'content' => [
            'title' => 'Content',
            'hint'  => 'Content, sections, entities',

            'sections' => [
                'title' => '🧩 Content sections',
                'desc'  => 'Enable/disable sections and page structure (binding via section_id).',
                'help'  => [
                    'A section is a “switch” for a block on the page (the <code>is_active</code> field).',
                    'Entities (banners/sliders/cards/…) are bound via <code>section_id</code> — important for display order.',
                    'If a section is disabled — the block is hidden on the front-end and records are not loaded.',
                ],
            ],

            'blocks' => [
                'title' => '📝 Content blocks',
                'desc'  => 'Website texts (EN/RU/RO). Rendered on the front-end via :raw.',
                'help'  => [
                    'Edit the text — the result is visible on the front-end after refresh.',
                    'For line breaks use HTML: <code>&lt;br&gt;</code> or lists.',
                    'Fill RU/RO/EN consistently to avoid “empty” locales.',
                ],
            ],

            'banners' => [
                'title' => '🖼 Banners',
                'desc'  => 'Hero banners and positions. The CTA button is shown once outside the carousel.',
                'help'  => [
                    'Keep an eye on <code>position</code> — it defines the slide order on the front-end.',
                    'The CTA button must be shown <b>only once</b> (outside owl-carousel).',
                    'After replacing images, check responsiveness (mobile).',
                ],
            ],

            'sliders_cards' => [
                'title' => '🎞 Sliders / 🧱 Cards',
                'desc'  => 'Sliders and service cards. Locale + positions + sections.',
                'help'  => [
                    'Each record has <code>locale</code> — ensure RU/RO/EN are filled.',
                    'Positions (<code>position</code>) should be sequential for a predictable order.',
                    'If a block disappears — check the section (Content sections) and the <code>section_id</code> binding.',
                ],
            ],
        ],

        'access' => [
            'title' => 'Access',
            'hint'  => 'Change history',

            'history' => [
                'title' => '🕘 History',
                'desc'  => 'History: create/update/delete, old/new values, rollback (SUPER_ADMIN only).',
                'help'  => [
                    'History records <code>created/updated/deleted</code> events for models.',
                    'Rollback is available to SUPER_ADMIN only.',
                    'To understand what changed — check <code>old_values</code> and <code>new_values</code>.',
                ],
            ],
        ],
    ],

    'common' => [
        'go'          => 'Go →',
        'open'        => 'Open →',
        'sections'    => 'Sections →',
        'how_to'      => 'How to work properly',
        'instruction' => 'Instruction',
        'tips'        => 'Tips',
        'open_arrow'  => 'Open →',
        'dash'        => '—',
    ],
];
