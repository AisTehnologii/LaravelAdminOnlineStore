<div class="glass-2 dash-card dash-hover">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <div class="text-sm font-semibold text-white/90">
                    {{ __('dashboard.widgets.recent_actions.title') }}
                </div>
                <span class="dash-pill">
                    {{ __('dashboard.widgets.recent_actions.badge') }}
                </span>
            </div>

            <div class="muted text-sm mt-1 leading-relaxed">
                {{ __('dashboard.widgets.recent_actions.subtitle') }}
            </div>
        </div>

        <a href="{{ url('/admin/revisions') }}" class="dash-btn dash-btn-amber">
            {{ __('dashboard.common.open_arrow') }}
        </a>
    </div>

    <div class="mt-4 space-y-2">
        @forelse($items as $r)
            @php
                $model = $this->humanModel($r->revisionable_type);
                $who   = $r->user?->name ?? __('dashboard.widgets.recent_actions.system');
                $event = strtoupper((string) $r->event);
                $url   = $this->revisionUrl((int) $r->id);
            @endphp

            <a href="{{ $url }}" class="block">
                <div class="glass dash-mini dash-hover flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm text-white/90 truncate">
                            <span class="text-amber-200 font-semibold">{{ $who }}</span>
                            <span class="muted2">•</span>
                            <span class="dash-pill">{{ $event }}</span>
                            <span class="text-amber-200">({{ $model }} #{{ $r->revisionable_id }})</span>
                        </div>
                        <div class="text-xs muted2">
                            {{ __('dashboard.widgets.common.time') }}
                            {{ optional($r->created_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>

                    <span class="dash-pill">→</span>
                </div>
            </a>
        @empty
            <div class="muted text-sm">
                {{ __('dashboard.widgets.recent_actions.empty') }}
            </div>
        @endforelse
    </div>
</div>
