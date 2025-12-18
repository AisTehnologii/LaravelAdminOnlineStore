@php
    /** @var \App\Models\TaskComment $comment */
    $comment = $getRecord();
    $name = $comment?->user?->name ?? 'System';
    $initial = mb_strtoupper(mb_substr($name, 0, 1));
@endphp

<div class="flex gap-3 py-3">
    {{-- Avatar --}}
    <div class="shrink-0">
        <div class="w-9 h-9 rounded-full bg-amber-500/20 flex items-center justify-center text-sm font-semibold text-amber-300">
            {{ $initial }}
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-white/90">
                {{ $name }}
            </div>
            <div class="text-xs text-white/40">
                {{ $comment->created_at?->format('d.m.Y H:i') }}
            </div>
        </div>

        <div class="mt-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/90 whitespace-pre-wrap">
            {{ $comment->body }}
        </div>

        {{-- Attachments --}}
        @if(!empty($comment->attachments) && is_array($comment->attachments))
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($comment->attachments as $file)
                    @php
                        // FileUpload сохраняет относительные пути (например: task-comments/xxx.png)
                        $url = asset('storage/' . ltrim($file, '/'));
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['png','jpg','jpeg','webp','gif']);
                    @endphp

                    @if($isImage)
                        <a href="{{ $url }}" target="_blank" class="block">
                            <img
                                src="{{ $url }}"
                                alt="attachment"
                                class="h-24 max-w-[240px] object-cover rounded-lg border border-white/10 hover:opacity-90 transition"
                            />
                        </a>
                    @else
                        <a
                            href="{{ $url }}"
                            download
                            class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-white/80 hover:bg-white/10 transition"
                        >
                            📎 {{ basename($file) }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
