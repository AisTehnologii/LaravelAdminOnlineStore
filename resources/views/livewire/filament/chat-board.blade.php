<div class="chat-grid">

    {{-- LEFT --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- USERS --}}
        <div class="chat-card">
            <div class="chat-title" style="margin-bottom:10px; opacity:.85;">Пользователи</div>

            <div class="chat-list">
                @forelse($users as $user)
                    @php
                        $isActive = ($selectedUserId === $user->id);
                        $uUnread = (int) (($userUnread[$user->id] ?? 0));
                    @endphp

                    <button
                        wire:click="startChatWithUser({{ $user->id }})"
                        class="chat-item {{ $isActive ? 'is-active' : '' }}"
                        type="button"
                    >
                        <div class="chat-item-inner" style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
                            <div style="min-width:0;">
                                <div class="chat-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $user->name }}
                                </div>
                                <div class="chat-sub">Нажми чтобы начать чат</div>
                            </div>

                            @if($uUnread > 0)
                                <span class="chat-badge">{{ $uUnread }}</span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="chat-empty">Нет пользователей</div>
                @endforelse
            </div>
        </div>

        {{-- CONVERSATIONS --}}
        <div class="chat-card">
            <div class="chat-title" style="margin-bottom:10px; opacity:.85;">Диалоги</div>

            <div class="chat-list">
                @forelse($conversations as $c)
                    @php
                        $isActive = ($activeConversationId === $c->id);
                        $title = $dialogTitles[$c->id] ?? ('Чат #'.$c->id);
                        $unread = (int) ($unreadByConversation[$c->id] ?? 0);
                    @endphp

                    <button
                        wire:click="openConversation({{ $c->id }})"
                        class="chat-item {{ $isActive ? 'is-active' : '' }}"
                        type="button"
                    >
                        <div class="chat-item-inner" style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
                            <div style="min-width:0;">
                                <div class="chat-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $title }}
                                </div>
                                <div class="chat-sub">{{ strtoupper($c->type) }} • #{{ $c->id }}</div>
                            </div>

                            @if($unread > 0)
                                <span class="chat-badge">{{ $unread }}</span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="chat-empty">Диалогов пока нет</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="chat-card chat-panel">

        <div class="chat-header">
            <div class="chat-title" style="opacity:.85;">
                @if($activeConversationId)
                    {{ $activeTitle ?? ('Чат #'.$activeConversationId) }}
                @else
                    Чат
                @endif
            </div>
            <div class="chat-sub">{{ now()->format('d.m.Y H:i') }}</div>
        </div>

        <div class="chat-messages" id="chatMessages">
            @if(! $activeConversationId)
                <div class="chat-empty">Выбери пользователя или диалог слева 👈</div>
            @else
                @forelse($messages as $m)
                    @php
                        $isMine = ((int) $m->user_id === (int) auth()->id());
                        $rs = $readStatusByMessage[$m->id] ?? null;
                    @endphp

                    <div class="msg-row {{ $isMine ? 'mine' : '' }}">
                        <div class="msg-bubble">
                            <div class="msg-meta">{{ $m->user->name }} • {{ $m->created_at->format('H:i') }}</div>

                            @if(trim((string)$m->body) !== '')
                                <div class="msg-text">{{ $m->body }}</div>
                            @endif

                            {{-- attachments --}}
                            @if($m->attachments && $m->attachments->count())
                                <div style="margin-top:10px; display:flex; flex-direction:column; gap:10px;">
                                    @foreach($m->attachments as $att)
                                        @php
                                            $publicUrl = asset('storage/' . ltrim($att->path, '/'));
                                            $downloadUrl = route('chat.attachments.download', $att->id);
                                            $isImage = is_string($att->mime) && str_starts_with($att->mime, 'image/');
                                        @endphp

                                        @if($isImage)
                                            <a href="{{ $publicUrl }}" target="_blank" style="display:inline-block;">
                                                <img
                                                    src="{{ $publicUrl }}"
                                                    alt="{{ $att->original_name }}"
                                                    style="max-width:340px; border-radius:12px; border:1px solid rgba(255,255,255,.10); display:block;"
                                                />
                                            </a>
                                        @else
                                            <a
                                                href="{{ $downloadUrl }}"
                                                style="color: rgba(253,230,138,.95); text-decoration: underline;"
                                            >
                                                📎 {{ $att->original_name }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- ✅ статус "Отправлено / Просмотрено" только для моих сообщений --}}
                            @if($isMine)
                                @php
                                    // personal: показываем Просмотрено/Отправлено
                                    // group: Просмотрено X/Y
                                    $isGroup = ($activeConversationType === 'group');
                                @endphp

                                <div style="margin-top:8px; font-size:12px; color: rgba(255,255,255,.55); text-align:right;">
                                    @if($isGroup && $rs)
                                        Просмотрено {{ $rs['seenCount'] }}/{{ $rs['total'] }}
                                    @else
                                        @if($rs && $rs['seen'])
                                            Просмотрено
                                        @else
                                            Отправлено
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="chat-empty">Сообщений пока нет — напиши первым 🙂</div>
                @endforelse
            @endif
        </div>

        {{-- выбранные файлы перед отправкой --}}
        @if(count($uploads) > 0)
            <div style="padding:10px 0; display:flex; flex-wrap:wrap; gap:8px;">
                @foreach($uploads as $i => $f)
                    <span class="chat-badge" style="display:flex; gap:8px; align-items:center;">
                        {{ $f->getClientOriginalName() }}
                        <button type="button" class="chat-btn" style="padding:4px 8px;" wire:click="removeUpload({{ $i }})">✕</button>
                    </span>
                @endforeach
            </div>
        @endif

        {{-- emoji panel --}}
        @if($showEmoji)
            <div style="padding:10px 0; display:flex; flex-wrap:wrap; gap:6px;">
                @foreach(['😀','😁','😂','😊','😍','😘','😎','🤝','🔥','✅','❗','🎯','💡','📌','📎','📷','🙏','💪'] as $e)
                    <button type="button" class="chat-btn" style="padding:6px 10px" wire:click="addEmoji('{{ $e }}')">{{ $e }}</button>
                @endforeach
            </div>
        @endif

        <div class="chat-inputbar">
            <button type="button" class="chat-btn" wire:click="toggleEmoji">😊</button>

            <label class="chat-btn" style="cursor:pointer;">
                📎
                <input type="file" multiple wire:model="uploads" style="display:none;">
            </label>

            <textarea
                wire:model.defer="newMessage"
                class="chat-input"
                rows="1"
                placeholder="Сообщение…"
                @disabled(! $activeConversationId)
                data-chat-input="1"
            ></textarea>

            <button
                wire:click="send"
                class="chat-btn"
                @disabled(! $activeConversationId)
                type="button"
            >
                Отправить
            </button>
        </div>

        {{-- JS: Enter отправляет, Shift+Enter перенос + автоскролл вниз --}}
        <script>
            (function () {
                function scrollDown() {
                    const box = document.getElementById('chatMessages');
                    if (!box) return;
                    box.scrollTop = box.scrollHeight;
                }

                function bind() {
                    const el = document.querySelector('textarea[data-chat-input="1"]');
                    if (!el) return;

                    if (el.dataset.bound === "1") return;
                    el.dataset.bound = "1";

                    el.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            Livewire.dispatch('sendMessage');
                            setTimeout(scrollDown, 150);
                        }
                    });

                    scrollDown();
                }

                document.addEventListener('livewire:navigated', bind);
                document.addEventListener('DOMContentLoaded', bind);
                document.addEventListener('livewire:load', bind);
                document.addEventListener('livewire:update', () => setTimeout(scrollDown, 100));

                setTimeout(bind, 300);
            })();
        </script>

    </div>

</div>
