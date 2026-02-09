<style>
/* ==========================================================================
   AIS / Filament Admin Styles (Light + Dark) — FINAL
   - Dark: diagonal gradient (top-left black -> bottom-right dark beige)
   - Light: replace any blue/cyan accents with black
   - Chat included (your custom .chat-* classes)
   ========================================================================== */

/* ------------------------------
   1) Base tokens (LIGHT default)
   ------------------------------ */
:root {
    --ais-bg: #f6f3ed;
    --ais-surface: #ffffff;
    --ais-surface-2: rgba(255, 255, 255, 0.86);

    --ais-border: rgba(0, 0, 0, 0.10);
    --ais-border-strong: rgba(0, 0, 0, 0.18);

    --ais-text: #111111;
    --ais-text-soft: rgba(17, 17, 17, 0.72);
    --ais-text-mute: rgba(17, 17, 17, 0.55);

    /* LIGHT accent: black */
    --ais-primary: #111111;
    --ais-primary-dark: #000000;
    --ais-primary-soft: rgba(17, 17, 17, 0.10);

    --ais-success: #1f7a46;
    --ais-warning: #8a5a10;
    --ais-danger: #b42318;

    --ais-shadow: 0 18px 45px rgba(0, 0, 0, 0.10);
    --ais-shadow-soft: 0 10px 28px rgba(0, 0, 0, 0.08);

    --ais-radius: 16px;

    --ais-link: #000000;
    --ais-link-hover: rgba(0,0,0,0.85);
}

/* ------------------------------
   2) DARK tokens
   ------------------------------ */
html.dark {
    --ais-bg: #050506;
    --ais-surface: rgba(18, 18, 20, 0.70);
    --ais-surface-2: rgba(18, 18, 20, 0.55);

    --ais-border: rgba(255, 255, 255, 0.10);
    --ais-border-strong: rgba(255, 255, 255, 0.18);

    --ais-text: #ffffff;
    --ais-text-soft: rgba(255, 255, 255, 0.78);
    --ais-text-mute: rgba(255, 255, 255, 0.55);

    /* Warm-neutral accent */
    --ais-primary: #d8bf9d;
    --ais-primary-dark: #caa97f;
    --ais-primary-soft: rgba(216, 191, 157, 0.12);

    --ais-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
    --ais-shadow-soft: 0 12px 36px rgba(0, 0, 0, 0.28);

    /* In dark you asked white text everywhere */
    --ais-link: #ffffff;
    --ais-link-hover: rgba(255,255,255,0.85);
}

/* ==========================================================================
   3) BODY / BACKGROUND
   ========================================================================== */

html:not(.dark) body.fi-body {
    background: var(--ais-bg) !important;
    color: var(--ais-text) !important;
}

html.dark body.fi-body {
    background:
        radial-gradient(
            circle at 0% 0%,
            rgba(5, 5, 6, 0.95) 0%,
            rgba(12, 12, 14, 0.75) 35%,
            transparent 65%
        ),
        radial-gradient(
            circle at 100% 100%,
            rgba(216, 191, 157, 0.85) 0%,
            rgba(216, 191, 157, 0.45) 40%,
            transparent 70%
        ),
        linear-gradient(
            135deg,
            #050506 0%,
            #0b0c10 35%,
            #1a1a1c 65%,
            #c6a57a 100%
        ) !important;

    color: var(--ais-text) !important;
}

.fi-main { background: transparent !important; }

/* ==========================================================================
   4) TOPBAR
   ========================================================================== */

.fi-topbar { backdrop-filter: blur(14px); }

html:not(.dark) .fi-topbar {
    background: rgba(255, 255, 255, 0.65);
    border-bottom: 1px solid var(--ais-border);
}

html.dark .fi-topbar {
    background: rgba(12, 12, 14, 0.55);
    border-bottom: 1px solid var(--ais-border);
}

/* ==========================================================================
   5) SIDEBAR
   ========================================================================== */

.fi-sidebar { border-right: 1px solid var(--ais-border); }

html:not(.dark) .fi-sidebar {
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(14px);
}

html.dark .fi-sidebar {
    background: rgba(12, 12, 14, 0.55);
    backdrop-filter: blur(14px);
}

.fi-sidebar-group-label {
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-weight: 700;
}

.fi-sidebar-item-button {
    border-radius: 14px;
    transition: transform .15s ease, background .15s ease, box-shadow .15s ease, border-color .15s ease;
}

/* LIGHT active = black */
html:not(.dark) .fi-sidebar-item-button.fi-active {
    background: linear-gradient(90deg, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0.10) 60%, rgba(0,0,0,0.06) 100%);
    color: #000000 !important;
    border: 1px solid rgba(0,0,0,0.20);
    box-shadow: 0 10px 24px rgba(0,0,0,0.14);
}

/* DARK active = beige */
html.dark .fi-sidebar-item-button.fi-active {
    background: linear-gradient(90deg, rgba(216,191,157,0.20) 0%, rgba(216,191,157,0.10) 60%, rgba(216,191,157,0.06) 100%);
    color: var(--ais-text) !important;
    border: 1px solid rgba(216,191,157,0.20);
    box-shadow: 0 12px 32px rgba(0,0,0,0.30);
}

.fi-sidebar-item-button:hover { transform: translateY(-1px); }

/* ==========================================================================
   6) CARDS / PANELS / MODALS
   ========================================================================== */

.fi-section,
.fi-card,
.fi-ta-ctn,
.fi-modal-window,
.fi-fo-component-ctn {
    border-radius: var(--ais-radius) !important;
    border: 1px solid var(--ais-border) !important;
    box-shadow: var(--ais-shadow-soft) !important;
}

html:not(.dark) .fi-section,
html:not(.dark) .fi-card,
html:not(.dark) .fi-ta-ctn,
html:not(.dark) .fi-modal-window,
html:not(.dark) .fi-fo-component-ctn {
    background: var(--ais-surface-2) !important;
    color: var(--ais-text) !important;
}

html.dark .fi-section,
html.dark .fi-card,
html.dark .fi-ta-ctn,
html.dark .fi-modal-window,
html.dark .fi-fo-component-ctn {
    background: var(--ais-surface) !important;
    color: var(--ais-text) !important;
}

/* ==========================================================================
   7) TABLES
   ========================================================================== */

.fi-ta-header { border-bottom: 1px solid var(--ais-border); }

.fi-ta-table thead th {
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 12px;
    color: var(--ais-text-mute) !important;
}

.fi-ta-table tbody tr { transition: background .12s ease; }

html:not(.dark) .fi-ta-table tbody tr:hover { background: rgba(0,0,0,0.04); }
html.dark .fi-ta-table tbody tr:hover { background: rgba(255,255,255,0.04); }

/* ==========================================================================
   8) FORMS / INPUTS
   ========================================================================== */

.fi-input,
.fi-select-input,
.fi-fo-textarea,
.fi-fo-rich-editor .trix-content,
.fi-fo-file-upload {
    border-radius: 14px !important;
    border: 1px solid var(--ais-border) !important;
    box-shadow: none !important;
}

html:not(.dark) .fi-input,
html:not(.dark) .fi-select-input,
html:not(.dark) .fi-fo-textarea,
html:not(.dark) .fi-fo-rich-editor .trix-content,
html:not(.dark) .fi-fo-file-upload {
    background: rgba(255,255,255,0.80) !important;
    color: var(--ais-text) !important;
}

html.dark .fi-input,
html.dark .fi-select-input,
html.dark .fi-fo-textarea,
html.dark .fi-fo-rich-editor .trix-content,
html.dark .fi-fo-file-upload {
    background: rgba(18,18,20,0.55) !important;
    color: var(--ais-text) !important;
}

/* focus */
.fi-input:focus,
.fi-select-input:focus,
.fi-fo-textarea:focus {
    outline: none !important;
    border-color: rgba(0,0,0,0.35) !important;
}
html.dark .fi-input:focus,
html.dark .fi-select-input:focus,
html.dark .fi-fo-textarea:focus {
    border-color: rgba(216,191,157,0.45) !important;
}

/* ==========================================================================
   9) BUTTONS
   ========================================================================== */

.fi-btn {
    border-radius: 999px !important;
    font-weight: 800;
    letter-spacing: 0.06em;
}

/* primary */
.fi-btn.fi-btn-primary {
    background: linear-gradient(135deg, var(--ais-primary), var(--ais-primary-dark)) !important;
    border: 1px solid rgba(0,0,0,0.20) !important;
    box-shadow: 0 12px 28px rgba(0,0,0,0.22) !important;
}

html.dark .fi-btn.fi-btn-primary {
    border: 1px solid rgba(216,191,157,0.24) !important;
    box-shadow: 0 14px 36px rgba(0,0,0,0.35) !important;
}

/* secondary */
.fi-btn.fi-btn-gray {
    background: rgba(0,0,0,0.06) !important;
    border: 1px solid var(--ais-border) !important;
    color: var(--ais-text) !important;
}
html.dark .fi-btn.fi-btn-gray { background: rgba(255,255,255,0.06) !important; }

/* ==========================================================================
   10) BADGES
   ========================================================================== */

.fi-badge { border-radius: 999px !important; font-weight: 800; }

html:not(.dark) .fi-badge-color-primary,
html:not(.dark) .fi-badge-color-success,
html:not(.dark) .fi-badge-color-warning,
html:not(.dark) .fi-badge-color-danger {
    background-color: rgba(0,0,0,0.08) !important;
    color: #000000 !important;
    border: 1px solid rgba(0,0,0,0.22) !important;
}

html.dark .fi-badge-color-primary {
    background-color: rgba(216,191,157,0.12) !important;
    color: var(--ais-text) !important;
    border: 1px solid rgba(216,191,157,0.22) !important;
}

/* ==========================================================================
   11) NOTIFICATIONS
   ========================================================================== */

.fi-notification {
    border-radius: var(--ais-radius) !important;
    border: 1px solid var(--ais-border) !important;
    box-shadow: var(--ais-shadow) !important;
}

html:not(.dark) .fi-notification { background: rgba(255,255,255,0.85) !important; }
html.dark .fi-notification { background: rgba(18,18,20,0.70) !important; }

/* ==========================================================================
   12) LINKS (Light black / Dark white)
   ========================================================================== */

a, .fi-link, .fi-ta-link, .fi-breadcrumbs a {
    color: var(--ais-link) !important;
}
a:hover, .fi-link:hover, .fi-ta-link:hover, .fi-breadcrumbs a:hover {
    color: var(--ais-link-hover) !important;
}

/* ==========================================================================
   13) HEADINGS / DIVIDERS
   ========================================================================== */

.fi-header-heading {
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.fi-divider, .fi-hr { border-color: var(--ais-border) !important; }

/* ==========================================================================
   14) CHAT (your custom classes)
   - Light: dark text
   - Dark: white text
   ========================================================================== */

.chat-grid { display: grid; grid-template-columns: 360px 1fr; gap: 16px; }
@media (max-width: 1100px) { .chat-grid { grid-template-columns: 1fr; } }

.chat-card {
    border: 1px solid var(--ais-border);
    background: var(--ais-surface);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 12px;
    color: var(--ais-text);
    box-shadow: var(--ais-shadow-soft);
}

html:not(.dark) .chat-card { background: rgba(255,255,255,0.86); }
html.dark .chat-card { background: rgba(18,18,20,0.55); }

.chat-list { display: flex; flex-direction: column; gap: 8px; max-height: 32vh; overflow: auto; padding-right: 4px; }

.chat-item {
    width: 100%;
    text-align: left;
    border-radius: 12px;
    padding: 10px 12px;
    border: 1px solid var(--ais-border);
    background: transparent;
    transition: transform .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease;
    position: relative;
    color: var(--ais-text);
}

.chat-item:hover {
    background: rgba(0,0,0,0.04);
    border-color: var(--ais-border-strong);
    transform: scale(1.01);
}
html.dark .chat-item:hover { background: rgba(255,255,255,0.05); }

.chat-item.is-active {
    background: var(--ais-primary-soft);
    border-color: rgba(0,0,0,0.20);
    box-shadow: 0 0 0 2px rgba(0,0,0,0.10), 0 8px 24px rgba(0,0,0,0.08);
}
html.dark .chat-item.is-active {
    border-color: rgba(216,191,157,0.35);
    box-shadow: 0 0 0 2px rgba(216,191,157,0.12), 0 10px 28px rgba(0,0,0,0.25);
}

.chat-item.is-active::before {
    content: "";
    position: absolute;
    left: 6px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 999px;
    background: var(--ais-primary);
    opacity: 0.9;
}

.chat-item .chat-item-inner { padding-left: 10px; }

.chat-title { font-size: 14px; font-weight: 700; color: var(--ais-text); }
.chat-sub   { font-size: 12px; color: var(--ais-text-mute); margin-top: 2px; }

.chat-badge {
    font-size: 12px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid var(--ais-border-strong);
    background: rgba(0,0,0,0.06);
    color: var(--ais-text);
}
html.dark .chat-badge {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.14);
}

.chat-panel { height: 74vh; display: flex; flex-direction: column; }
.chat-header { display:flex; align-items:center; justify-content:space-between; padding-bottom: 10px; border-bottom: 1px solid var(--ais-border); }
.chat-messages { flex: 1; overflow: auto; padding: 14px 4px 14px 0; display:flex; flex-direction:column; gap: 8px; }
.chat-empty { font-size: 14px; color: var(--ais-text-mute); }

.msg-row { display:flex; }
.msg-row.mine { justify-content:flex-end; }

.msg-bubble {
    max-width: 78%;
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid var(--ais-border);
    background: rgba(0,0,0,0.03);
    color: var(--ais-text);
}
html.dark .msg-bubble { background: rgba(255,255,255,0.06); }

.msg-row.mine .msg-bubble {
    background: var(--ais-primary-soft);
    border-color: rgba(0,0,0,0.18);
}
html.dark .msg-row.mine .msg-bubble {
    border-color: rgba(216,191,157,0.25);
}

.msg-meta { font-size: 12px; color: var(--ais-text-mute); margin-bottom: 4px; }
.msg-text { font-size: 14px; color: var(--ais-text); white-space: pre-wrap; }

.chat-inputbar { display:flex; gap: 10px; align-items:center; padding-top: 10px; border-top: 1px solid var(--ais-border); }

.chat-input {
    width: 100%;
    border-radius: 12px;
    border: 1px solid var(--ais-border);
    background: rgba(255,255,255,0.75);
    color: var(--ais-text);
    padding: 10px 12px;
    outline: none;
}
html.dark .chat-input { background: rgba(18,18,20,0.55); color: var(--ais-text); }

.chat-input::placeholder { color: var(--ais-text-mute); }

.chat-btn {
    border-radius: 12px;
    border: 1px solid var(--ais-border-strong);
    background: rgba(0,0,0,0.06);
    color: var(--ais-text);
    padding: 10px 14px;
    transition: background .15s ease, transform .1s ease;
    white-space: nowrap;
}
html.dark .chat-btn { background: rgba(255,255,255,0.08); }

.chat-btn:hover { background: rgba(0,0,0,0.10); transform: translateY(-1px); }
html.dark .chat-btn:hover { background: rgba(255,255,255,0.12); }

.chat-btn:disabled, .chat-input:disabled { opacity: .45; cursor: not-allowed; }

/* ==========================================================================
   15) SCROLLBAR
   ========================================================================== */
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: rgba(0,0,0,0.06); }
html.dark ::-webkit-scrollbar-track { background: rgba(255,255,255,0.06); }
::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--ais-primary), var(--ais-primary-dark));
    border-radius: 999px;
}
</style>
