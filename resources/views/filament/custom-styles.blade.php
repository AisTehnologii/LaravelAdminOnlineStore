<style>
    /********************************************************
     *  ПАЛИТРА АИС
     ********************************************************/

    :root {
        --ais-bg:            #f6f1e6;
        --ais-bg-soft:       #f6e7c9;
        --ais-bg-card:       #ffffff;
        --ais-bg-card-soft:  #fbf3e5;

        --ais-text-main:     #242425;
        --ais-text-soft:     #fbfcfcff;

        --ais-border-soft:   #e3d5c0;
        --ais-border-strong: #d1b894;

        --ais-primary:       #1fa09b;
        --ais-primary-soft:  rgba(31, 160, 155, 0.1);
        --ais-primary-dark:  #13827e;

        --ais-accent:        #f2a550;
        --ais-accent-soft:   rgba(242, 165, 80, 0.12);

        --ais-shadow-soft:   0 12px 30px rgba(0, 0, 0, 0.06);
        --ais-radius-card:   18px;
    }

    /********************************************************
     *  ОБЩИЙ ФОН И ТЕКСТ
     ********************************************************/

    body.fi-body {
        margin: 0;
        background:
            radial-gradient(circle at top left, rgba(250, 235, 215, 0.8), transparent 55%),
            radial-gradient(circle at bottom right, rgba(233, 203, 164, 0.9), transparent 55%),
            var(--ais-bg);
        color: var(--ais-text-main);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "SF Pro Text", "Roboto", sans-serif;
    }

    .fi-main {
        background: transparent;
    }

    .fi-main > .fi-page {
        padding-bottom: 3rem;
    }

    .fi-main,
    .fi-sidebar,
    .fi-topbar,
    .fi-section,
    .fi-btn,
    .fi-ta-table,
    .fi-account-dropdown-trigger,
    .fi-sidebar-item-button {
        transition:
            background-color .18s ease,
            color .18s ease,
            border-color .18s ease,
            box-shadow .18s ease,
            transform .1s ease;
    }

    /********************************************************
     *  САЙДБАР
     ********************************************************/

    .fi-sidebar {
        background:
            linear-gradient(180deg, #242425 0%, #2e2923 45%, #26221f 100%);
        color: #f5efe6;
        border-right: 1px solid rgba(0, 0, 0, 0.18);
        box-shadow: 8px 0 30px rgba(0, 0, 0, 0.28);
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(249, 250, 251, 0.06);
        padding-bottom: .8rem;
    }

    .fi-brand {
        color: #f6e7c9;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        font-size: .78rem;
    }

    .fi-sidebar-group-label {
        text-transform: uppercase;
        letter-spacing: .16em;
        font-size: .65rem;
        color: rgba(249, 250, 251, 0.65);
    }

    .fi-sidebar-item-button {
        border-radius: 999px;
        padding-inline: .9rem;
        padding-block: .55rem;
        background: transparent;
        color: rgba(249, 250, 251, 0.8);
    }

    .fi-sidebar-item-button .fi-sidebar-item-label {
        font-size: .86rem;
        font-weight: 500;
    }

    .fi-sidebar-item-button:hover {
        background: radial-gradient(circle at left, rgba(246, 231, 201, 0.18), transparent 60%);
        color: #ffffff;
    }

    .fi-sidebar-item-button.fi-active {
        background:
            linear-gradient(90deg, var(--ais-primary) 0%, #23b8b2 40%, #1fa09b 100%);
        color: #fefcf8;
        box-shadow:
            0 10px 25px rgba(0, 0, 0, 0.35),
            0 0 0 1px rgba(0, 0, 0, 0.35);
    }

    .fi-sidebar-item-button.fi-active .fi-sidebar-item-label {
        font-weight: 600;
    }

    .fi-sidebar-item-icon {
        color: inherit;
        opacity: .9;
    }

    /********************************************************
     *  TOPBAR — ОДИНАКОВО ТЁМНЫЙ В ЛЮБОЙ ТЕМЕ
     ********************************************************/

    .fi-topbar {
        backdrop-filter: blur(14px);
        background:
            linear-gradient(90deg, #1f2933 0%, #111827 60%, #020617 100%);
        border-bottom: 1px solid rgba(15, 23, 42, 0.8);
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.45);
    }

    .fi-topbar-heading {
        color: #f9fafb;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        font-size: .76rem;
    }

    .fi-topbar-subheading {
        color: #f7f7f7ff;
        font-size: .82rem;
    }

    /********************************************************
     *  КНОПКИ
     ********************************************************/

    .fi-btn.fi-btn-primary {
        border-radius: 999px;
        font-weight: 600;
        background: linear-gradient(135deg, var(--ais-primary), #22b3ad);
        border: none;
        box-shadow:
            0 10px 25px rgba(31, 160, 155, 0.35),
            0 0 0 1px rgba(24, 61, 59, 0.25);
        color: #fefcf8;
    }

    .fi-btn.fi-btn-primary:hover {
        filter: brightness(1.03);
        transform: translateY(-1px);
        box-shadow:
            0 14px 32px rgba(31, 160, 155, 0.45),
            0 0 0 1px rgba(24, 61, 59, 0.4);
    }

    .fi-btn.fi-btn-secondary,
    .fi-btn.fi-btn-outline {
        border-radius: 999px;
        background-color: rgba(255, 255, 255, 0.85);
        border-color: var(--ais-border-strong);
        color: var(--ais-text-main);
    }

    .fi-btn.fi-btn-secondary:hover,
    .fi-btn.fi-btn-outline:hover {
        background-color: #fffaf3;
    }

    /********************************************************
     *  КАРТОЧКИ / ФОРМЫ / ВИДЖЕТЫ
     ********************************************************/

    .fi-section {
        border-radius: var(--ais-radius-card);
        border: 1px solid var(--ais-border-soft);
        background:
            radial-gradient(circle at top left, rgba(246, 231, 201, 0.7), transparent 60%),
            var(--ais-bg-card);
        box-shadow: var(--ais-shadow-soft);
    }

    .fi-section-header {
        border-bottom-color: rgba(215, 186, 148, 0.6);
    }

    .fi-section-header .fi-section-header-heading {
        color: #3e3020;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        font-size: .78rem;
    }

    .fi-section-header .fi-section-header-description {
        color: var(--ais-text-soft);
        font-size: .82rem;
    }

    .fi-input,
    .fi-select,
    .fi-textarea {
        border-radius: 12px;
        border-color: var(--ais-border-soft);
        color: var(--ais-text-main);
    }

    .fi-input:focus,
    .fi-select:focus,
    .fi-textarea:focus {
        border-color: var(--ais-primary);
        box-shadow:
            0 0 0 1px rgba(31, 160, 155, 0.4),
            0 0 0 4px rgba(31, 160, 155, 0.12);
    }

    .fi-input::placeholder,
    .fi-textarea::placeholder {
        color: #f4f4f4ff;
    }

    .fi-fieldset-legend {
        color: #3e3020;
        font-weight: 500;
    }

    /********************************************************
     *  ТАБЛИЦЫ — ВСЕГДА ТЁМНЫЕ
     ********************************************************/

    .fi-ta-table {
        font-size: .86rem;
        border-radius: 16px;
        overflow: hidden;
        background-color: #020617; /* глубокий тёмный фон */
        color: #e5e7eb;            /* светлый текст */
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.5);
    }

    .fi-ta-table thead {
        background:
            linear-gradient(90deg, #111827, #020617);
    }

    .fi-ta-table th {
        font-weight: 600;
        color: #e5e7eb;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .1em;
        border-bottom-color: rgba(31, 41, 55, 0.9);
    }

    .fi-ta-table td {
        border-bottom-color: rgba(31, 41, 55, 0.85);
        vertical-align: middle;
        color: #e5e7eb;
    }

    .fi-ta-table tbody tr:nth-child(even) {
        background-color: #020617;
    }

    .fi-ta-table tbody tr:nth-child(odd) {
        background-color: #020617;
    }

    .fi-ta-table tbody tr:hover {
        background:
            linear-gradient(90deg, rgba(31, 41, 55, 0.95), rgba(15, 23, 42, 0.9));
    }

    /********************************************************
     *  БЭДЖИ
     ********************************************************/

    .fi-badge {
        border-radius: 999px;
        font-weight: 500;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .fi-badge-color-primary,
    .fi-badge-color-success {
        background-color: var(--ais-primary-soft);
        color: var(--ais-primary-dark);
        border: 1px solid rgba(31, 160, 155, 0.35);
    }

    .fi-badge-color-warning {
        background-color: var(--ais-accent-soft);
        color: #facc6b;
        border: 1px solid rgba(242, 165, 80, 0.5);
    }

    /********************************************************
     *  АККАУНТ / АВАТАР
     ********************************************************/

    .fi-account-dropdown-trigger {
        background:
            linear-gradient(135deg, var(--ais-primary), #24b7b0);
        color: #fefcf8;
        border-radius: 999px;
        box-shadow:
            0 10px 25px rgba(31, 160, 155, 0.35),
            0 0 0 1px rgba(24, 61, 59, 0.3);
        font-weight: 600;
        border: none;
    }

    .fi-account-dropdown-trigger:hover {
        filter: brightness(1.04);
        transform: translateY(-1px);
    }

    /********************************************************
     *  ХЛЕБНЫЕ КРОШКИ
     ********************************************************/

    .fi-breadcrumbs-item {
        color: var(--ais-text-soft);
        font-size: .78rem;
    }

    .fi-breadcrumbs-item-current {
        color: #f9fafb;
        font-weight: 500;
    }

    /********************************************************
     *  СКРОЛЛБАР
     ********************************************************/

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #111827;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, var(--ais-primary), var(--ais-accent));
        border-radius: 999px;
    }

    .more-39291 {
        display: none !important;
    }
</style>
