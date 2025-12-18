<div class="glass-2 dash-card dash-hover">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <div class="text-sm font-semibold text-white/90">👥 Пользователи онлайн</div>
                <span class="dash-pill dash-pill-amber">🟢 {{ $count }}</span>
                <span class="dash-pill">⏱ {{ $onlineWindowMinutes }} мин</span>
            </div>

            <div class="muted text-sm mt-1">
                Кто сейчас активен в панели — обновляется по last_seen_at.
            </div>
        </div>

        <span class="dash-pill">Live</span>
    </div>

    <div class="mt-4 space-y-2">
        @forelse($online as $u)
            <div class="glass dash-mini dash-hover flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="dash-pill">🟢</span>

                    <div class="min-w-0">
                        <div class="text-sm text-white/90 truncate">{{ $u->name }}</div>
                        <div class="text-xs muted2 truncate">{{ $u->email }}</div>
                    </div>
                </div>

                <span class="dash-pill">
                    🕒 {{ $u->last_seen_at ? \Carbon\Carbon::parse($u->last_seen_at)->format('H:i:s') : '—' }}
                </span>
            </div>
        @empty
            <div class="muted text-sm">Сейчас никого не видно онлайн.</div>
        @endforelse
    </div>
</div>
