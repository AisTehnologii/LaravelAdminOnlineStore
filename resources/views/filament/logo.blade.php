@php
    // путь к логотипу: public/images/marga-admin-logo.svg
    $logoUrl = asset('marga/images/whitelogoremovebg2.png');
@endphp

<div class="flex items-center gap-3">
    <div class="shrink-0 rounded-xl bg-white/5 p-1.5">
        <img
            src="{{ $logoUrl }}"
            alt="Marga Admin logo"
            class="h-11 w-11 object-contain"
        >
    </div>

    <span class="text-sm font-semibold tracking-[0.16em] uppercase text-slate-100" style="font-size: 20px;">
        Admin
    </span>
</div>
