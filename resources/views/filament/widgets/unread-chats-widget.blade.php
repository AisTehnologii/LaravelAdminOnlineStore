<div class="glass-2 dash-card dash-hover">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <div class="text-sm font-semibold text-white/90">
                    {{ __('dashboard.widgets.unread_chats.title') }}
                </div>

                <span class="dash-pill dash-pill-amber">
                    {{ __('dashboard.widgets.unread_chats.count', ['count' => $count]) }}
                </span>
            </div>

            <div class="muted text-sm mt-1 leading-relaxed">
                {{ __('dashboard.widgets.unread_chats.subtitle') }}
            </div>
        </div>

        <a href="{{ url('/admin/chats') }}" class="dash-btn dash-btn-amber">
            {{ __('dashboard.common.open_arrow') }}
        </a>
    </div>

    <div class="mt-4 space-y-2">
        @forelse($latest as $c)
            @php $last = $c->messages->first(); @endphp

            <div class="glass dash-mini dash-hover flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-sm text-white/90 truncate">
                        <span class="dash-pill">
                            {{ __('dashboard.widgets.unread_chats.dialog', ['id' => $c->id]) }}
                        </span>
                        <span class="text-amber-200">
                            {{ __('dashboard.widgets.unread_chats.unread') }}
                        </span>
                    </div>
                    <div class="text-xs muted2 truncate">
                        {{ $last?->body ?? __('dashboard.common.dash') }}
                    </div>
                </div>

                <span class="dash-pill dash-pill-amber">
                    {{ __('dashboard.widgets.unread_chats.reply') }}
                </span>
            </div>
        @empty
            <div class="muted text-sm">
                {{ __('dashboard.widgets.unread_chats.empty') }}
            </div>
        @endforelse
    </div>
</div>
