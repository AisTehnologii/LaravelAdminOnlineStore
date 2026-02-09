<div class="glass-2 dash-card dash-hover">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <div class="text-sm font-semibold text-white/90">
                    {{ __('dashboard.widgets.active_tasks.title') }}
                </div>

                <span class="dash-pill">
                    {{ __('dashboard.widgets.active_tasks.total', ['count' => $activeTotal]) }}
                </span>

                @if(!is_null($myActive))
                    <span class="dash-pill dash-pill-amber">
                        {{ __('dashboard.widgets.active_tasks.mine', ['count' => $myActive]) }}
                    </span>
                @endif
            </div>

            <div class="muted text-sm mt-1 leading-relaxed">
                {{ __('dashboard.widgets.active_tasks.subtitle') }}
            </div>
        </div>

        <a href="{{ url('/admin/tasks') }}" class="dash-btn dash-btn-amber">
            {{ __('dashboard.common.open_arrow') }}
        </a>
    </div>

    <div class="mt-4 space-y-2">
        @forelse($latest as $t)
            <div class="glass dash-mini dash-hover flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="dash-pill">#{{ $t->id }}</span>

                    <div class="min-w-0">
                        <div class="text-sm text-white/90 truncate">{{ $t->title }}</div>
                        <div class="text-xs muted2">
                            {{ __('dashboard.widgets.common.time') }}
                            {{ optional($t->created_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                <span class="dash-pill dash-pill-amber">
                    {{ __('dashboard.widgets.active_tasks.in_progress') }}
                </span>
            </div>
        @empty
            <div class="muted text-sm">
                {{ __('dashboard.widgets.active_tasks.empty') }}
            </div>
        @endforelse
    </div>
</div>
