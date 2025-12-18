<script>
    (function () {
        // ✅ всегда dark
        localStorage.setItem('theme', 'dark');

        // ✅ на всякий случай: если Filament/браузер не успели применить
        document.documentElement.classList.add('dark');

        // ✅ при навигации внутри панели (Livewire navigate)
        document.addEventListener('livewire:navigated', () => {
            localStorage.setItem('theme', 'dark');
            document.documentElement.classList.add('dark');
        });
    })();
</script>

<style>
    /* ✅ запрещаем переключатель темы (чтобы никто не включил light) */
    .fi-theme-switcher,
    [data-theme-switcher],
    button[title*="theme" i],
    button[title*="Theme" i] {
        display: none !important;
    }

    /* ✅ принудительно считаем тему тёмной (на уровне переменных) */
    :root { --default-theme-mode: dark !important; }

    /* ✅ приоритет твоих стилей над filament/app.css */
    body.fi-body,
    body.fi-body * {
        /* не трогаем всё подряд цветом,
           но даём твоим правилам преимущество, если есть конфликты */
    }

    /* пример: если у тебя sidebar должен быть всегда тёмный */
    .fi-sidebar {
        background: #0f172a !important;     /* подставь твой цвет */
        border-color: rgba(255,255,255,.08) !important;
    }

    .fi-sidebar .fi-sidebar-item-label,
    .fi-sidebar .fi-sidebar-group-label {
        color: rgba(255,255,255,.82) !important;
    }

    .fi-sidebar .fi-sidebar-item-button:hover {
        background: rgba(255,255,255,.06) !important;
    }

    .fi-sidebar .fi-sidebar-item-button[aria-current="page"] {
        background: rgba(245,158,11,.16) !important;
        border: 1px solid rgba(245,158,11,.35) !important;
    }
</style>



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

        --ais-primary: #505353;
        --ais-primary-soft:  rgba(31, 160, 155, 0.1);
        --ais-primary-dark:  #13827e;

        --ais-accent: #675139;
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
            linear-gradient(90deg, #111213ff, #3b3d44ff);
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



<style>
  /* Layout */
  .chat-grid { display: grid; grid-template-columns: 360px 1fr; gap: 16px; }
  @media (max-width: 1100px) { .chat-grid { grid-template-columns: 1fr; } }

  .chat-card {
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.05);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 12px;
  }

  .chat-list { display: flex; flex-direction: column; gap: 8px; max-height: 32vh; overflow: auto; padding-right: 4px; }

  .chat-item {
    width: 100%;
    text-align: left;
    border-radius: 12px;
    padding: 10px 12px;
    border: 1px solid rgba(255,255,255,.10);
    background: transparent;
    transition: transform .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease;
    position: relative;
  }

  .chat-item:hover {
    background: rgba(255,255,255,.05);
    border-color: rgba(255,255,255,.20);
    transform: scale(1.01);
  }

  .chat-item.is-active {
    background: rgba(245,158,11,.22); /* amber */
    border-color: rgba(245,158,11,.55);
    box-shadow: 0 0 0 2px rgba(253,230,138,.25), 0 8px 24px rgba(245,158,11,.08);
    transform: scale(1.01);
  }

  .chat-item.is-active::before {
    content: "";
    position: absolute;
    left: 6px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 999px;
    background: rgba(72, 71, 64, 0.95);
  }

  .chat-item .chat-item-inner { padding-left: 10px; }

  .chat-title { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.92); }
  .chat-sub   { font-size: 12px; color: rgba(255,255,255,.55); margin-top: 2px; }

  .chat-badge {
    font-size: 12px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid rgba(245,158,11,.55);
    background: rgba(245,158,11,.18);
    color: rgba(255,255,255,.92);
  }

  /* Chat area */
  .chat-panel { height: 74vh; display: flex; flex-direction: column; }
  .chat-header { display:flex; align-items:center; justify-content:space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,.10); }
  .chat-messages { flex: 1; overflow: auto; padding: 14px 4px 14px 0; display:flex; flex-direction:column; gap: 8px; }
  .chat-empty { font-size: 14px; color: rgba(255,255,255,.55); }

  .msg-row { display:flex; }
  .msg-row.mine { justify-content:flex-end; }
  .msg-bubble {
    max-width: 78%;
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.05);
  }
  .msg-row.mine .msg-bubble {
    background: rgba(245,158,11,.15);
    border-color: rgba(245,158,11,.30);
  }
  .msg-meta { font-size: 12px; color: rgba(255,255,255,.55); margin-bottom: 4px; }
  .msg-text { font-size: 14px; color: rgba(255,255,255,.92); white-space: pre-wrap; }

  .chat-inputbar { display:flex; gap: 10px; align-items:center; padding-top: 10px; border-top: 1px solid rgba(255,255,255,.10); }
  .chat-input {
    width: 100%;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.06);
    color: rgba(255,255,255,.92);
    padding: 10px 12px;
    outline: none;
  }
  .chat-input::placeholder { color: rgba(255,255,255,.45); }
  .chat-btn {
    border-radius: 12px;
    border: 1px solid rgba(245,158,11,.45);
    background: rgba(245,158,11,.16);
    color: rgba(255,255,255,.92);
    padding: 10px 14px;
    transition: background .15s ease;
    white-space: nowrap;
  }
  .chat-btn:hover { background: rgba(245,158,11,.22); }
  .chat-btn:disabled, .chat-input:disabled { opacity: .45; cursor: not-allowed; }

  /********************************************************
 *  3) SIDEBAR — красивее/контрастнее (как ты просила)
 ********************************************************/
.fi-sidebar {
    background:
        radial-gradient(circle at top left, rgba(255,255,255,0.07), transparent 55%),
        linear-gradient(180deg, #2f2d2cff 0%, #2d2a28ff 55%, #34322fff 100%) !important;
    color: #f5efe6;
    border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 10px 0 32px rgba(0, 0, 0, 0.32) !important;
}

.fi-sidebar-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding-bottom: .9rem;
}

.fi-brand {
    color: rgba(246, 231, 201, 0.92) !important;
    font-weight: 800 !important;
    letter-spacing: .20em !important;
    text-transform: uppercase;
    font-size: .78rem;
}

.fi-sidebar-group-label {
    margin-top: 14px;
    text-transform: uppercase;
    letter-spacing: .22em;
    font-size: .62rem;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.62) !important;
}

.fi-sidebar-item-button {
    position: relative;
    border-radius: 999px;
    padding-inline: 1rem;
    padding-block: .58rem;
    padding-left: 18px !important; /* место под маркер */
    background: transparent;
    color: rgba(255, 255, 255, 0.86) !important;
    border: 1px solid rgba(255,255,255,0.06);
    transition: transform .12s ease, background .16s ease, border-color .16s ease, box-shadow .16s ease;
}

.fi-sidebar-item-button .fi-sidebar-item-label {
    font-size: .88rem;
    font-weight: 650;
    color: rgba(255, 255, 255, 0.86) !important;
}

.fi-sidebar-item-icon {
    color: rgba(255, 255, 255, 0.76) !important;
    opacity: 1;
}

/* hover */
.fi-sidebar-item-button:hover {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(0,0,0,0.18);
}

/* active */
.fi-sidebar-item-button.fi-active {
    background: linear-gradient(90deg, rgba(31,160,155,1) 0%, rgba(35,184,178,1) 45%, rgba(31,160,155,1) 100%) !important;
    border-color: rgba(255,255,255,0.12) !important;
    color: #ffffff !important;
    box-shadow:
        0 14px 30px rgba(0, 0, 0, 0.30),
        0 0 0 2px rgba(31, 160, 155, 0.18);
}

.fi-sidebar-item-button.fi-active .fi-sidebar-item-label {
    color: #ffffff !important;
    font-weight: 800 !important;
}

.fi-sidebar-item-button.fi-active .fi-sidebar-item-icon {
    color: rgba(255,255,255,0.95) !important;
}

/* маркер слева у active */
.fi-sidebar-item-button.fi-active::before {
    content: "";
    position: absolute;
    left: 10px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 999px;
    background: rgba(246,231,201,0.95);
}
/********************************************************
 *  9) SCROLLBAR
 ********************************************************/
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: rgba(255,255,255,0.06); }
::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--ais-primary), var(--ais-accent));
    border-radius: 999px;
}


</style>
