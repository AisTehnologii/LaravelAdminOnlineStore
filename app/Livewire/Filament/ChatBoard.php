<?php

namespace App\Livewire\Filament;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatBoard extends Component
{
    use WithFileUploads;

    public ?int $activeConversationId = null;
    public ?int $selectedUserId = null;

    public string $newMessage = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $uploads = [];

    public bool $showEmoji = false;

    public function getListeners(): array
    {
        $userId = Auth::id();

        $listeners = [
            'sendMessage' => 'send',
        ];

        if ($userId) {
            $listeners["echo-private:inbox.{$userId},message.sent"] = 'incoming';
        }

        return $listeners;
    }

    private function canUseChat(): bool
    {
        return Auth::check();
    }

    public function mount(): void
    {
        $first = $this->conversationsQuery()->first();
        $this->activeConversationId = $first?->id;

        if ($this->activeConversationId) {
            $this->markRead($this->activeConversationId);
        }
    }

    private function conversationsQuery()
    {
        $u = Auth::user();

        if ($u->can('chat.view_all')) {
            return Conversation::query()->latest('updated_at');
        }

        return Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('users.id', $u->id))
            ->latest('updated_at');
    }

    public function openConversation(int $conversationId): void
    {
        $this->authorizeAccess($conversationId);

        $this->activeConversationId = $conversationId;
        $this->selectedUserId = null;

        $this->markRead($conversationId);
    }

    public function startChatWithUser(int $userId): void
    {
        $this->selectedUserId = $userId;

        abort_unless($this->canUseChat(), 403);

        $me = Auth::id();
        if ($me === $userId) return;

        $conversation = Conversation::query()
            ->where('type', 'personal')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $me))
            ->whereHas('participants', fn ($q) => $q->where('users.id', $userId))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'type' => 'personal',
                'created_by' => $me,
            ]);

            $conversation->participants()->attach([
                $me => ['role' => 'member', 'last_read_at' => null],
                $userId => ['role' => 'member', 'last_read_at' => null],
            ]);
        }

        $this->activeConversationId = $conversation->id;
        $this->markRead($conversation->id);
    }

    public function toggleEmoji(): void
    {
        $this->showEmoji = ! $this->showEmoji;
    }

    public function addEmoji(string $emoji): void
    {
        $this->newMessage .= $emoji;
    }

    public function removeUpload(int $index): void
    {
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    public function send(): void
    {
        $conversationId = $this->activeConversationId;
        if (! $conversationId) return;

        $this->authorizeSend($conversationId);

        $text = trim($this->newMessage);

        if ($text === '' && count($this->uploads) === 0) {
            return;
        }

        $this->validate([
            'uploads.*' => 'file|max:20480', // 20MB
        ], [
            'uploads.*.max' => 'Файл слишком большой (max 20MB)',
        ]);

        DB::transaction(function () use ($conversationId, $text) {
            $msg = Message::create([
                'conversation_id' => $conversationId,
                'user_id' => Auth::id(),
                'body' => $text,
            ]);

            foreach ($this->uploads as $file) {
                $original = $file->getClientOriginalName() ?? 'file';
                $ext = $file->getClientOriginalExtension();

                $safeName = Str::slug(pathinfo($original, PATHINFO_FILENAME));
                $safeName = $safeName !== '' ? $safeName : 'file';

                $finalName = $safeName . '-' . Str::random(6) . ($ext ? ".{$ext}" : '');

                $path = $file->storeAs(
                    "chat/{$conversationId}/" . now()->format('Y-m'),
                    $finalName,
                    'public'
                );

                MessageAttachment::create([
                    'message_id' => $msg->id,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $original,
                    'mime' => $file->getMimeType(),
                    'size' => (int) $file->getSize(),
                ]);
            }

            // sender read
            $this->markRead($conversationId);

            // поднять диалог вверх
            Conversation::whereKey($conversationId)->update(['updated_at' => now()]);

            broadcast(new MessageSent($msg))->toOthers();
        });

        $this->newMessage = '';
        $this->uploads = [];
        $this->showEmoji = false;
    }

    public function incoming(): void
    {
        if ($this->activeConversationId) {
            $this->markRead($this->activeConversationId);
        }
    }

    private function markRead(int $conversationId): void
    {
        DB::table('conversation_participants')
            ->where('conversation_id', $conversationId)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);
    }

    private function authorizeAccess(int $conversationId): void
    {
        $conv = Conversation::findOrFail($conversationId);
        abort_unless(Auth::user()->can('view', $conv), 403);
    }

    private function authorizeSend(int $conversationId): void
    {
        $conv = Conversation::findOrFail($conversationId);
        abort_unless(Auth::user()->can('sendMessage', $conv), 403);
    }

    public function render()
    {
        abort_unless($this->canUseChat(), 403);

        $meId = Auth::id();

        $users = User::query()
            ->where('id', '!=', $meId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $conversations = $this->conversationsQuery()
            ->with(['participants:id,name'])
            ->get();

        // titles
        $dialogTitles = [];
        foreach ($conversations as $c) {
            if ($c->type === 'personal') {
                $other = $c->participants->firstWhere('id', '!=', $meId);
                $dialogTitles[$c->id] = $other?->name ?? 'Личный чат';
            } elseif ($c->type === 'group') {
                $dialogTitles[$c->id] = $c->title ?: 'Группа';
            } elseif ($c->type === 'support') {
                $dialogTitles[$c->id] = 'Support';
            } else {
                $dialogTitles[$c->id] = strtoupper($c->type) . ' #' . $c->id;
            }
        }

        // unread per conversation
        $unreadByConversation = DB::table('conversation_participants as cp')
            ->join('messages as m', 'm.conversation_id', '=', 'cp.conversation_id')
            ->where('cp.user_id', $meId)
            ->where('m.user_id', '!=', $meId)
            ->whereColumn('m.created_at', '>', DB::raw("COALESCE(cp.last_read_at, '1970-01-01 00:00:00')"))
            ->groupBy('cp.conversation_id')
            ->selectRaw('cp.conversation_id as conversation_id, COUNT(*) as cnt')
            ->pluck('cnt', 'conversation_id')
            ->toArray();

        // active title
        $activeTitle = null;
        if ($this->activeConversationId) {
            $activeTitle = $dialogTitles[$this->activeConversationId] ?? ('Чат #'.$this->activeConversationId);
        }

        // unread per user (personal chats)
        $userUnread = [];
        foreach ($conversations as $c) {
            if ($c->type !== 'personal') continue;

            $other = $c->participants->firstWhere('id', '!=', $meId);
            if (! $other) continue;

            $cnt = (int) ($unreadByConversation[$c->id] ?? 0);
            if ($cnt <= 0) continue;

            $userUnread[$other->id] = ($userUnread[$other->id] ?? 0) + $cnt;
        }

        $messages = collect();
        $readStatusByMessage = []; // message_id => ['seen'=>bool, 'seenCount'=>int, 'total'=>int]
        $activeConversationType = null;

        if ($this->activeConversationId) {
            $this->authorizeAccess($this->activeConversationId);

            // ВАЖНО: подтягиваем pivot last_read_at участников именно активного чата
            $activeConversation = Conversation::query()
                ->with(['participants' => function ($q) {
                    $q->select('users.id', 'users.name')
                      ->withPivot('last_read_at');
                }])
                ->findOrFail($this->activeConversationId);

            $activeConversationType = $activeConversation->type;

            $participants = $activeConversation->participants; // collection users with pivot
            $others = $participants->filter(fn ($u) => (int) $u->id !== (int) $meId)->values();
            $othersTotal = $others->count();

            $messages = Message::query()
                ->where('conversation_id', $this->activeConversationId)
                ->with([
                    'user:id,name',
                    'attachments:id,message_id,disk,path,original_name,mime,size',
                ])
                ->oldest()
                ->get();

            // Считаем "просмотрено" для КАЖДОГО моего сообщения
            foreach ($messages as $m) {
                if ((int) $m->user_id !== (int) $meId) {
                    continue;
                }

                $seenCount = 0;

                foreach ($others as $ou) {
                    $lr = $ou->pivot?->last_read_at;
                    if ($lr && $lr >= $m->created_at) {
                        $seenCount++;
                    }
                }

                $readStatusByMessage[$m->id] = [
                    'seen' => ($othersTotal > 0 && $seenCount === $othersTotal),
                    'seenCount' => $seenCount,
                    'total' => $othersTotal,
                ];
            }
        }

        return view('livewire.filament.chat-board', compact(
            'users',
            'conversations',
            'messages',
            'dialogTitles',
            'unreadByConversation',
            'activeTitle',
            'userUnread',
            'readStatusByMessage',
            'activeConversationType',
        ));
    }
}
