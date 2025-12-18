<x-filament-panels::page>
    @php
        $u = auth()->user();
        $name = $u?->name ?? 'User';
        $email = $u?->email ?? null;
        $roles = method_exists($u, 'getRoleNames') ? $u->getRoleNames() : collect();
        $roleText = $roles->isNotEmpty() ? $roles->implode(', ') : '—';
        $now = now()->format('d.m.Y H:i');

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
            color: rgba(255,255,255,.92) !important;
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
            color: rgba(255,255,255,.75) !important;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .dash-pill-amber {
            border-color: rgba(245,158,11,.30) !important;
            background: rgba(245,158,11,.12) !important;
            color: rgba(255,241,214,.95) !important;
        }

        .dash-hover {
            transition: border-color .15s ease, background .15s ease, transform .15s ease;
        }
        .dash-hover:hover {
            background: rgba(255,255,255,.05) !important;
            border-color: rgba(245,158,11,.20) !important;
            transform: translateY(-1px);
        }

        .muted  { color: rgba(255,255,255,.62) !important; }
        .muted2 { color: rgba(255,255,255,.45) !important; }
        .h-title { letter-spacing: .2px; }
        .section-title { font-weight: 700; color: rgba(255,255,255,.92) !important; }
        .sub-title { color: rgba(255,255,255,.62) !important; }

        /* раскрывашка */
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
        .dash-help ul {
            margin-top: 8px;
            padding-left: 16px;
            list-style: disc;
        }
        .dash-help li { margin: 4px 0; color: rgba(255,255,255,.72); font-size: 13px; line-height: 1.35; }
    </style>

    <div class="dash-shell space-y-6">

        {{-- HERO --}}
        <div class="glass soft-shadow dash-section dash-pad">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="space-y-3">
                    <div class="text-2xl md:text-3xl font-semibold text-white/95 h-title">
                        Добро пожаловать, <span class="text-amber-300">{{ $name }}</span> 👋
                    </div>

                    <div class="muted text-sm leading-relaxed max-w-[70ch]">
                        Это административная панель сайта. Здесь ты управляешь контентом, пользователями, ролями,
                        общением (чат) и задачами команды.
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <span class="dash-pill">🕒 {{ $now }}</span>
                        @if($email)
                            <span class="dash-pill">✉️ {{ $email }}</span>
                        @endif
                        <span class="dash-pill dash-pill-amber">🛡 Роль: {{ $roleText }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 md:justify-end">
                    <a href="{{ $urlChats }}" class="dash-btn dash-btn-amber">💬 Открыть чат</a>
                    <a href="{{ $urlTasks }}" class="dash-btn">✅ Открыть задачи</a>
                </div>

            </div>

            <div class="accent-line"></div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="glass-2 dash-mini">
                    <div class="text-xs muted2 uppercase tracking-wide">Подсказка</div>
                    <div class="mt-1 text-sm text-white/85">
                        Начни с <span class="text-amber-200">Content blocks</span> — там основные тексты сайта.
                    </div>
                </div>

                <div class="glass-2 dash-mini">
                    <div class="text-xs muted2 uppercase tracking-wide">Команда</div>
                    <div class="mt-1 text-sm text-white/85">
                        Для вопросов — <span class="text-amber-200">Chats</span>, для задач — <span class="text-amber-200">Tasks</span>.
                    </div>
                </div>

                <div class="glass-2 dash-mini">
                    <div class="text-xs muted2 uppercase tracking-wide">Права</div>
                    <div class="mt-1 text-sm text-white/85">
                        Роли и доступы настраиваются в <span class="text-amber-200">Roles</span> (Shield).
                    </div>
                </div>
            </div>
        </div>

        {{-- WIDGETS --}}
        <div class="glass dash-section dash-pad">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="section-title text-lg">Сводка</div>
                    <div class="sub-title text-sm mt-1">Задачи, непрочитанные, онлайн и последние действия.</div>
                </div>
                <span class="dash-pill">📌 Live</span>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @livewire(\App\Filament\Widgets\ActiveTasksWidget::class)
                @livewire(\App\Filament\Widgets\UnreadChatsWidget::class)
                @livewire(\App\Filament\Widgets\OnlineUsersWidget::class)
                @livewire(\App\Filament\Widgets\RecentRevisionsWidget::class)
            </div>
        </div>

        {{-- QUICK START (раскрываемые инструкции) --}}
        <div class="glass dash-section dash-pad">
            <div>
                <div class="section-title text-lg">Быстрый старт</div>
                <div class="sub-title text-sm mt-1">Кликни на карточку — раскроется подробная инструкция.</div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- CARD: Content blocks --}}
                <div
                    class="glass-2 dash-card dash-hover dash-expand"
                    x-data="{ open: false }"
                    @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                📝 Обновить тексты сайта
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                Открой <span class="text-amber-200">Content blocks</span> и меняй тексты (HTML поддерживается).
                            </div>
                        </div>
                        <div class="text-2xl">🧩</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlBlocks }}" @click.stop>Перейти →</a>
                        <a class="dash-btn" href="{{ $urlSections }}" @click.stop>Секции →</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">Как правильно редактировать</div>
                        <ul>
                            <li>Выбирай нужную <b>секцию</b> (например: <i>home.about_card</i>, <i>layout.footer</i>) и нужное поле.</li>
                            <li>Тексты на фронте выводятся через <b>HTML</b>. Можно вставлять ссылки, списки, переносы строк.</li>
                            <li>Если вставляешь HTML — проверяй, чтобы не было незакрытых тегов.</li>
                            <li>После правок — обнови фронт и проверь RU/RO/EN.</li>
                        </ul>
                    </div>
                </div>

                {{-- CARD: Chats --}}
                <div
                    class="glass-2 dash-card dash-hover dash-expand"
                    x-data="{ open: false }"
                    @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                💬 Общение в чате
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                Личные чаты и support-чаты. Вложения, эмодзи, непрочитанные, быстрые ответы.
                            </div>
                        </div>
                        <div class="text-2xl">💬</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlChats }}" @click.stop>Открыть →</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">Как работать</div>
                        <ul>
                            <li>Слева: пользователи и диалоги. Справа: сообщения.</li>
                            <li><b>Enter</b> — отправка, <b>Shift+Enter</b> — новая строка.</li>
                            <li>Файлы/картинки можно прикреплять — они сохраняются в storage и скачиваются через защищённый маршрут.</li>
                            <li>Непрочитанные считаются по <i>last_read_at</i> участника диалога.</li>
                        </ul>
                    </div>
                </div>

                {{-- CARD: Tasks --}}
                <div
                    class="glass-2 dash-card dash-hover dash-expand"
                    x-data="{ open: false }"
                    @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                ✅ Задачи команды
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                Создание задач, назначение ответственных, комментарии и вложения прямо в View.
                            </div>
                        </div>
                        <div class="text-2xl">✅</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlTasks }}" @click.stop>Открыть →</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">Как вести задачи</div>
                        <ul>
                            <li>В задаче фиксируй: <b>что сделать</b>, <b>срок</b>, <b>ответственного</b>, <b>вложения</b>.</li>
                            <li>В <b>View</b> задачи добавляй комментарии (кнопка видна всем авторизованным), это история работы.</li>
                            <li>Вложения к комментариям — чтобы не терялись файлы в чатах.</li>
                        </ul>
                    </div>
                </div>

                {{-- CARD: Users/Roles --}}
                <div
                    class="glass-2 dash-card dash-hover dash-expand"
                    x-data="{ open: false }"
                    @click="open = !open"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                👤 Пользователи и роли
                                <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                            </div>
                            <div class="muted text-sm mt-1 leading-relaxed">
                                Управление пользователями, назначение ролей (Shield), SUPER_ADMIN через is_admin.
                            </div>
                        </div>
                        <div class="text-2xl">🛡</div>
                    </div>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a class="dash-btn dash-btn-amber" href="{{ $urlUsers }}" @click.stop>Users →</a>
                        <a class="dash-btn" href="{{ $urlRoles }}" @click.stop>Roles →</a>
                    </div>

                    <div class="dash-help" x-show="open" x-collapse>
                        <div class="text-xs muted2 uppercase tracking-wide">Советы</div>
                        <ul>
                            <li>SUPER_ADMIN (is_admin=1) проходит любые проверки через Gate::before.</li>
                            <li>Остальным пользователям права даём через роли/permissions (Shield).</li>
                            <li>Если после генерации прав что-то “не видно” — обычно помогает permission cache reset / optimize:clear.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- LEFT MENU / СТРУКТУРА --}}
        <div class="glass dash-section dash-pad">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="section-title text-lg">Что где находится</div>
                    <div class="sub-title text-sm mt-1">Кликни на карточку — раскроется описание раздела.</div>
                </div>
                <span class="dash-pill">🧭 Навигация</span>
            </div>

            <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Content --}}
                <div class="glass-2 dash-card">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-semibold text-white/90">Content</div>
                        <div class="text-xs muted2">Контент, секции, сущности</div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Content sections --}}
                        <div
                            class="glass dash-mini dash-hover dash-expand"
                            x-data="{ open: false }"
                            @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        🧩 Content sections
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        Включение/выключение секций и структура страниц (привязка по section_id).
                                    </div>
                                </div>
                                <a href="{{ $urlSections }}" class="dash-btn dash-btn-amber" @click.stop>Открыть →</a>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">Инструкция</div>
                                <ul>
                                    <li>Секция = “переключатель” блока на странице (is_active).</li>
                                    <li>Сущности (banners/sliders/cards/…) привязаны к section_id — это важно для порядка вывода.</li>
                                    <li>Если секция выключена — на фронте блок не показываем и записи не подгружаем.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Content blocks --}}
                        <div
                            class="glass dash-mini dash-hover dash-expand"
                            x-data="{ open: false }"
                            @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        📝 Content blocks
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        Тексты сайта (EN/RU/RO). Вывод на фронте через
                                        @verbatim{!! !!}@endverbatim
                                    </div>
                                </div>
                                <a href="{{ $urlBlocks }}" class="dash-btn dash-btn-amber" @click.stop>Открыть →</a>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">Инструкция</div>
                                <ul>
                                    <li>Меняем текст — сразу видим результат на фронте (после обновления страницы).</li>
                                    <li>Если нужно “переносы” — лучше использовать HTML: <code>&lt;br&gt;</code> или список.</li>
                                    <li>Сначала правим RU/RO/EN по очереди, чтобы не было “пустых” локалей.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Banners --}}
                        <div
                            class="glass dash-mini dash-hover dash-expand"
                            x-data="{ open: false }"
                            @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        🖼 Banners
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        Hero-баннеры и позиции. CTA-кнопка выводится 1 раз вне карусели.
                                    </div>
                                </div>
                                <a href="{{ $urlBanners }}" class="dash-btn dash-btn-amber" @click.stop>Открыть →</a>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">Инструкция</div>
                                <ul>
                                    <li>Следи за <b>position</b> — порядок слайдов на фронте.</li>
                                    <li>CTA-кнопка на фронте должна быть <b>одна</b> (вне owl-carousel).</li>
                                    <li>Если меняешь фон/изображения — проверь адаптив (mobile).</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Sliders / Cards --}}
                        <div
                            class="glass dash-mini dash-hover dash-expand"
                            x-data="{ open: false }"
                            @click="open = !open"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                        🎞 Sliders / 🧱 Cards
                                        <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                    </div>
                                    <div class="muted text-sm mt-1 leading-relaxed">
                                        Слайдеры и карточки сервисов. Локаль + позиции + секции.
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ $urlSliders }}" class="dash-btn dash-btn-amber" @click.stop>Sliders →</a>
                                    <a href="{{ $urlCards }}" class="dash-btn" @click.stop>Cards →</a>
                                </div>
                            </div>

                            <div class="dash-help" x-show="open" x-collapse>
                                <div class="text-xs muted2 uppercase tracking-wide">Инструкция</div>
                                <ul>
                                    <li>У каждой записи есть locale — проверь, что RU/RO/EN заполнены.</li>
                                    <li>Позиции (position) должны идти без “скачков”, чтобы порядок был предсказуемый.</li>
                                    <li>Если блок исчез — проверь секцию (Content sections) и привязку section_id.</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Access / History --}}
                <div class="glass-2 dash-card">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-semibold text-white/90">Access</div>
                        <div class="text-xs muted2">Лог изменений</div>
                    </div>

                    <div
                        class="mt-4 glass dash-mini dash-hover dash-expand"
                        x-data="{ open: false }"
                        @click="open = !open"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-white/90 flex items-center gap-2">
                                    🕘 History
                                    <span class="dash-chevron" :class="{ 'is-open': open }">›</span>
                                </div>
                                <div class="muted text-sm mt-1 leading-relaxed">
                                    История изменений: create/update/delete, old/new values, rollback (SUPER_ADMIN).
                                </div>
                            </div>
                            <a href="{{ $urlHistory }}" class="dash-btn dash-btn-amber" @click.stop>Открыть →</a>
                        </div>

                        <div class="dash-help" x-show="open" x-collapse>
                            <div class="text-xs muted2 uppercase tracking-wide">Инструкция</div>
                            <ul>
                                <li>История пишется по моделям (created/updated/deleted).</li>
                                <li>Rollback доступен только SUPER_ADMIN.</li>
                                <li>Если нужно понять “что поменяли” — смотри old_values/new_values.</li>
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
                setInterval(ping, 25000); // каждые 25 секунд
            })();
        </script>
    @endpush
</x-filament-panels::page>
