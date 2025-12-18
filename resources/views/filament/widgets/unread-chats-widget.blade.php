<div class="glass-2 dash-card dash-hover">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <div class="text-sm font-semibold text-white/90">💬 Непрочитанные чаты</div>
                <span class="dash-pill dash-pill-amber">🔔 {{ $count }}</span>
            </div>

            <div class="muted text-sm mt-1 leading-relaxed">
                Диалоги, где есть новые сообщения — открой и ответь.
            </div>
        </div>

        <a href="{{ url('/admin/chats') }}" class="dash-btn dash-btn-amber">Открыть →</a>
    </div>

    <div class="mt-4 space-y-2">
        @forelse($latest as $c)
            @php $last = $c->messages->first(); @endphp

            <div class="glass dash-mini dash-hover flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-sm text-white/90 truncate">
                        <span class="dash-pill">Диалог #{{ $c->id }}</span>
                        <span class="text-amber-200">unread</span>
                    </div>
                    <div class="text-xs muted2 truncate">
                        {{ $last?->body ?? '—' }}
                    </div>
                </div>

                <span class="dash-pill dash-pill-amber">Ответить</span>
            </div>
        @empty
            <div class="muted text-sm">Непрочитанных чатов нет.</div>
        @endforelse
    </div>
</div>
