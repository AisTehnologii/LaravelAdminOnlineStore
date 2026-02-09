<x-filament-panels::page>
    @php
        $u = auth()->user();
        $name = $u?->name ?? 'User';
        $email = $u?->email ?? null;

        $roles = method_exists($u, 'getRoleNames') ? $u->getRoleNames() : collect();
        $roleText = $roles->isNotEmpty() ? $roles->implode(', ') : '—';

        $urlChats    = url('/admin/chats');
        $urlTasks    = url('/admin/tasks');
        $urlBlocks   = url('/admin/content-blocks');
        $urlSections = url('/admin/content-sections');
        $urlBanners  = url('/admin/banners');
        $urlSliders  = url('/admin/sliders');
        $urlCards    = url('/admin/cards');
        $urlUsers    = url('/admin/users');
        $urlRoles    = url('/admin/roles');
        $urlHistory  = url('/admin/revisions'); // у тебя так
    @endphp

<style>
    .dash-shell { max-width: 1200px; }

    .dash-section { border-radius: 22px !important; padding: 22px !important; }
    .dash-card    { border-radius: 18px !important; padding: 18px !important; }
    .dash-mini    { border-radius: 16px !important; padding: 14px !important; }

    /* ====== БЛОКИ: как раньше ====== */
    .glass {
        background: rgba(255,255,255,.06) !important;
        border: 1px solid rgba(255,255,255,.10) !important;
        backdrop-filter: blur(10px) !important;
    }
    .glass-2 {
        background: rgba(0,0,0,.20) !important;
        border: 1px solid rgba(255,255,255,.10) !important;
    }
    .soft-shadow { box-shadow: 0 12px 30px rgba(0,0,0,.28) !important; }

    .accent-line {
        background: linear-gradient(90deg, rgba(245,158,11,.65), rgba(245,158,11,.10), rgba(255,255,255,.05));
        height: 1px;
        margin: 10px !important;
    }

    .dash-card { margin-top: 20px !important; }

    .dash-btn {
        border-radius: 14px !important;
        padding: 10px 14px !important;
        border: 1px solid rgba(255,255,255,.12) !important;
        background: rgba(0,0,0,.22) !important;
        transition: transform .15s ease, background .15s ease, border-color .15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        line-height: 1;
        white-space: nowrap;
    }
    .dash-btn:hover {
        background: rgba(255,255,255,.07) !important;
        border-color: rgba(255,255,255,.18) !important;
        transform: translateY(-1px);
    }
    .dash-btn-amber {
        border-color: rgba(245,158,11,.35) !important;
        background: rgba(245,158,11,.14) !important;
    }
    .dash-btn-amber:hover {
        background: rgba(245,158,11,.18) !important;
        border-color: rgba(245,158,11,.55) !important;
    }

    .dash-pill {
        border-radius: 999px !important;
        padding: 6px 10px !important;
        border: 1px solid rgba(255,255,255,.12) !important;
        background: rgba(0,0,0,.22) !important;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .dash-pill-amber {
        border-color: rgba(245,158,11,.30) !important;
        background: rgba(245,158,11,.12) !important;
    }

    .dash-hover {
        transition: border-color .15s ease, background .15s ease, transform .15s ease;
    }
    .dash-hover:hover {
        background: rgba(255,255,255,.05) !important;
        border-color: rgba(245,158,11,.20) !important;
        transform: translateY(-1px);
    }

    .h-title { letter-spacing: .2px; }

    /* TEXT adaptive */
    .dash-shell .section-title { font-weight: 700; color: rgba(0,0,0,.92) !important; }
    .dash-shell .sub-title     { color: rgba(0,0,0,.62) !important; }
    .dash-shell .muted         { color: rgba(0,0,0,.62) !important; }
    .dash-shell .muted2        { color: rgba(0,0,0,.45) !important; }

    .dash-shell .dash-btn,
    .dash-shell .dash-pill {
        color: rgba(0,0,0,.88) !important;
    }

    html.dark .dash-shell .section-title { color: rgba(255,255,255,.92) !important; }
    html.dark .dash-shell .sub-title     { color: rgba(255,255,255,.62) !important; }
    html.dark .dash-shell .muted         { color: rgba(255,255,255,.62) !important; }
    html.dark .dash-shell .muted2        { color: rgba(255,255,255,.45) !important; }

    html.dark .dash-shell .dash-btn,
    html.dark .dash-shell .dash-pill {
        color: rgba(255,255,255,.92) !important;
    }

    .dash-expand { cursor: pointer; }
    .dash-chevron {
        transition: transform .15s ease;
        opacity: .85;
    }
    .dash-chevron.is-open { transform: rotate(90deg); }
    .dash-help {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,.08);
    }
    html:not(.dark) .dash-help { border-top-color: rgba(0,0,0,.10) !important; }

    .dash-help ul {
        margin-top: 8px;
        padding-left: 16px;
        list-style: disc;
    }
    .dash-help li {
        margin: 4px 0;
        font-size: 13px;
        line-height: 1.35;
        color: rgba(0,0,0,.72) !important;
    }
    html.dark .dash-help li { color: rgba(255,255,255,.72) !important; }
</style>

    <div class="dash-shell space-y-6">

        {{-- HERO --}}
        <div class="glass soft-shadow dash-section dash-pad">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="space-y-3">
                    <div class="text-2xl md:text-3xl font-semibold text-white/95 h-title">
                        {!! __('dashboard.hero.welcome', [
                            'name' => '<span class="text-amber-300">'.e($name).'</span>'
                        ]) !!}
                    </div>

                    <div class="muted text-sm leading-relaxed max-w-[70ch]">
                        {{ __('dashboard.hero.intro') }}
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        @if($email)
                            <span class="dash-pill">
                                {{ __('dashboard.hero.email', ['email' => $email]) }}
                            </span>
                        @endif

                        <span class="dash-pill dash-pill-amber">
                            {{ __('dashboard.hero.role', ['role' => $roleText]) }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 md:justify-end">
                    <a href="{{ $urlChats }}" class="dash-btn dash-btn-amber">
                        {{ __('dashboard.hero.actions.open_chat') }}
                    </a>
                    <a href="{{ $urlTasks }}" class="dash-btn">
                        {{ __('dashboard.hero.actions.open_tasks') }}
                    </a>
                </div>

            </div>

            <div class="accent-line"></div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="glass-2 dash-mini">
                    <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.hero.cards.hint.title') }}</div>
                    <div class="mt-1 text-sm text-white/85">
                        {!! __('dashboard.hero.cards.hint.text', [
                            'blocks' => '<span class="text-amber-200">Content blocks</span>'
                        ]) !!}
                    </div>
                </div>

                <div class="glass-2 dash-mini">
                    <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.hero.cards.team.title') }}</div>
                    <div class="mt-1 text-sm text-white/85">
                        {!! __('dashboard.hero.cards.team.text', [
                            'chats' => '<span class="text-amber-200">Chats</span>',
                            'tasks' => '<span class="text-amber-200">Tasks</span>',
                        ]) !!}
                    </div>
                </div>

                <div class="glass-2 dash-mini">
                    <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.hero.cards.permissions.title') }}</div>
                    <div class="mt-1 text-sm text-white/85">
                        {!! __('dashboard.hero.cards.permissions.text', [
                            'roles' => '<span class="text-amber-200">Roles</span>',
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- WIDGETS --}}
        <div class="glass dash-section dash-pad">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="section-title text-lg">{{ __('dashboard.widgets.title') }}</div>
                    <div class="sub-title text-sm mt-1">{{ __('dashboard.widgets.subtitle') }}</div>
                </div>
                <span class="dash-pill">{{ __('dashboard.widgets.live') }}</span>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @livewire(\App\Filament\Widgets\ActiveTasksWidget::class)
                @livewire(\App\Filament\Widgets\UnreadChatsWidget::class)
                @livewire(\App\Filament\Widgets\OnlineUsersWidget::class)
                @livewire(\App\Filament\Widgets\RecentRevisionsWidget::class)
            </div>
        </div>

        {{-- QUICK START --}}
        <div class="glass dash-section dash-pad">
            <div>
                <div class="section-title text-lg">{{ __('dashboard.quick.title') }}</div>
                <div class="sub-title text-sm mt-1">{{ __('dashboard.quick.subtitle') }}</div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Content blocks --}}
                <div class="glass-2 dash-card dash-hover dash-expand"
                     x-data="{ open: false }"
                     @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                {{ __('dashboard.quick.cards.blocks.title') }}
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                {!! __('dashboard.quick.cards.blocks.desc', [
                                    'blocks' => '<span class="text-amber-200">Content blocks</span>',
                                ]) !!}
                            </div>
                        </div>
                        <div class="text-2xl">🧩</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlBlocks }}" @click.stop>{{ __('dashboard.common.go') }}</a>
                        <a class="dash-btn" href="{{ $urlSections }}" @click.stop>{{ __('dashboard.common.sections') }}</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.how_to') }}</div>
                        <ul>
                            @foreach(trans('dashboard.quick.cards.blocks.help') as $li)
                                <li>{!! $li !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Chats --}}
                <div class="glass-2 dash-card dash-hover dash-expand"
                     x-data="{ open: false }"
                     @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                {{ __('dashboard.quick.cards.chats.title') }}
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                {{ __('dashboard.quick.cards.chats.desc') }}
                            </div>
                        </div>
                        <div class="text-2xl">💬</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlChats }}" @click.stop>{{ __('dashboard.common.open') }}</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.how_to') }}</div>
                        <ul>
                            @foreach(trans('dashboard.quick.cards.chats.help') as $li)
                                <li>{!! $li !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Tasks --}}
                <div class="glass-2 dash-card dash-hover dash-expand"
                     x-data="{ open: false }"
                     @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                {{ __('dashboard.quick.cards.tasks.title') }}
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                {{ __('dashboard.quick.cards.tasks.desc') }}
                            </div>
                        </div>
                        <div class="text-2xl">✅</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlTasks }}" @click.stop>{{ __('dashboard.common.open') }}</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.how_to') }}</div>
                        <ul>
                            @foreach(trans('dashboard.quick.cards.tasks.help') as $li)
                                <li>{!! $li !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Users/Roles --}}
                <div class="glass-2 dash-card dash-hover dash-expand"
                     x-data="{ open: false }"
                     @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                {{ __('dashboard.quick.cards.users_roles.title') }}
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                {{ __('dashboard.quick.cards.users_roles.desc') }}
                            </div>
                        </div>
                        <div class="text-2xl">🛡</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlUsers }}" @click.stop>Users →</a>
                        <a class="dash-btn" href="{{ $urlRoles }}" @click.stop>Roles →</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.tips') }}</div>
                        <ul>
                            @foreach(trans('dashboard.quick.cards.users_roles.help') as $li)
                                <li>{!! $li !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- STRUCTURE --}}
        <div class="glass dash-section dash-pad">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="section-title text-lg">{{ __('dashboard.structure.title') }}</div>
                    <div class="sub-title text-sm mt-1">{{ __('dashboard.structure.subtitle') }}</div>
                </div>
                <span class="dash-pill">{{ __('dashboard.structure.badge') }}</span>
            </div>

            <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Content --}}
                <div class="glass-2 dash-card">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-semibold text-white/90">{{ __('dashboard.structure.content.title') }}</div>
                        <div class="text-xs muted2">{{ __('dashboard.structure.content.hint') }}</div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Content sections --}}
                        <div class="glass dash-mini dash-hover dash-expand"
                             x-data="{ open: false }"
                             @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        {{ __('dashboard.structure.content.sections.title') }}
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        {{ __('dashboard.structure.content.sections.desc') }}
                                    </div>
                                </div>
                                <a href="{{ $urlSections }}" class="dash-btn dash-btn-amber" @click.stop>{{ __('dashboard.common.open') }}</a>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.instruction') }}</div>
                                <ul>
                                    @foreach(trans('dashboard.structure.content.sections.help') as $li)
                                        <li>{!! $li !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Content blocks --}}
                        <div class="glass dash-mini dash-hover dash-expand"
                             x-data="{ open: false }"
                             @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        {{ __('dashboard.structure.content.blocks.title') }}
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        {!! __('dashboard.structure.content.blocks.desc', [
                                            'raw' => '@verbatim{!! !!}@endverbatim'
                                        ]) !!}
                                    </div>
                                </div>
                                <a href="{{ $urlBlocks }}" class="dash-btn dash-btn-amber" @click.stop>{{ __('dashboard.common.open') }}</a>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.instruction') }}</div>
                                <ul>
                                    @foreach(trans('dashboard.structure.content.blocks.help') as $li)
                                        <li>{!! $li !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Banners --}}
                        <div class="glass dash-mini dash-hover dash-expand"
                             x-data="{ open: false }"
                             @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        {{ __('dashboard.structure.content.banners.title') }}
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        {{ __('dashboard.structure.content.banners.desc') }}
                                    </div>
                                </div>
                                <a href="{{ $urlBanners }}" class="dash-btn dash-btn-amber" @click.stop>{{ __('dashboard.common.open') }}</a>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.instruction') }}</div>
                                <ul>
                                    @foreach(trans('dashboard.structure.content.banners.help') as $li)
                                        <li>{!! $li !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Sliders / Cards --}}
                        <div class="glass dash-mini dash-hover dash-expand"
                             x-data="{ open: false }"
                             @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        {{ __('dashboard.structure.content.sliders_cards.title') }}
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        {{ __('dashboard.structure.content.sliders_cards.desc') }}
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ $urlSliders }}" class="dash-btn dash-btn-amber" @click.stop>Sliders →</a>
                                    <a href="{{ $urlCards }}" class="dash-btn" @click.stop>Cards →</a>
                                </div>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.instruction') }}</div>
                                <ul>
                                    @foreach(trans('dashboard.structure.content.sliders_cards.help') as $li)
                                        <li>{!! $li !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Access / History --}}
                <div class="glass-2 dash-card">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-semibold text-white/90">{{ __('dashboard.structure.access.title') }}</div>
                        <div class="text-xs muted2">{{ __('dashboard.structure.access.hint') }}</div>
                    </div>

                    <div class="mt-4 glass dash-mini dash-hover dash-expand"
                         x-data="{ open: false }"
                         @click="open = !open"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                    {{ __('dashboard.structure.access.history.title') }}
                                    <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                </div>
                                <div class="muted text-sm mt-1 leading-relaxed">
                                    {{ __('dashboard.structure.access.history.desc') }}
                                </div>
                            </div>
                            <a href="{{ $urlHistory }}" class="dash-btn dash-btn-amber" @click.stop>{{ __('dashboard.common.open') }}</a>
                        </div>

                        <div class="dash-help" x-show="open" x-collapse>
                            <div class="text-xs muted2 uppercase tracking-wide">{{ __('dashboard.common.instruction') }}</div>
                            <ul>
                                @foreach(trans('dashboard.structure.access.history.help') as $li)
                                    <li>{!! $li !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- пинг для last_seen_at (онлайн) --}}
    @push('scripts')
        <script>
            (function () {
                const pingUrl = @json(route('admin.ping'));
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                async function ping() {
                    try {
                        await fetch(pingUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                            body: JSON.stringify({ t: Date.now() }),
                            keepalive: true,
                        });
                    } catch (e) {}
                }

                ping();
                setInterval(ping, 25000);
            })();
        </script>
    @endpush
</x-filament-panels::page>
