<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('maintenance.public.title') }}</title>
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
        font-family:ui-sans-serif,system-ui;background:#0b0f12;color:#fff;}
        .card{max-width:720px;padding:28px;border-radius:18px;background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.08);box-shadow:0 20px 80px rgba(0,0,0,.45)}
        .muted{color:rgba(255,255,255,.72);line-height:1.6}
        .badge{display:inline-flex;gap:8px;align-items:center;padding:6px 10px;border-radius:999px;
        background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.25);color:#fbbf24;font-weight:600}
        h1{margin:14px 0 10px;font-size:28px}
    </style>
</head>
<body>
<div class="card">
    <div class="badge">🛠 {{ __('maintenance.public.badge') }}</div>
    <h1>{{ __('maintenance.public.heading') }}</h1>
    <div class="muted">
        {{ $message ?: __('maintenance.public.default_message') }}
    </div>
</div>
</body>
</html>
