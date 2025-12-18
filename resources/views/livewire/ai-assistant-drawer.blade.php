<template x-teleport="body">
    <div x-data="{ open: @entangle('open') }">

        {{-- КНОПКА справа (стрелка) --}}
        <div class="pointer-events-auto"
             style="position:fixed; top:50%; right:16px; left:auto; transform:translateY(-50%); z-index:99999;">
            <button type="button"
                    @click="open = !open"
                    style="width:44px; height:44px;"
                    class="rounded-full border border-white/15 bg-black/80 backdrop-blur shadow-lg hover:bg-black/95 transition"
                    title="AI Assistant">
                <span class="block text-white text-lg" x-text="open ? '→' : '←'"></span>
            </button>
        </div>

        {{-- OVERLAY --}}
        <div x-show="open"
             x-transition.opacity
             @click="open=false"
             style="position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:99998;"
        ></div>

        {{-- DRAWER справа --}}
        <div x-show="open"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             style="position:fixed; top:0; right:0; left:auto; height:100dvh; width:420px; max-width:95vw; z-index:99999;"
             class="bg-[#0E1117] border-l border-white/15 shadow-[0_0_40px_rgba(0,0,0,0.6)] pointer-events-auto">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
                <div class="text-white/90 font-semibold">AI Assistant</div>
                <button class="text-white/60 hover:text-white" @click="open=false">✕</button>
            </div>

            {{-- Messages --}}
            <div class="p-4 space-y-3 overflow-y-auto"
                 style="height: calc(100dvh - 132px);"
                 x-data
                 x-on:ai-scroll-bottom.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })">

                @foreach($this->messages as $m)
                    @php $isUser = $m->role === 'user'; @endphp
                    <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                        <div class="{{ $isUser ? 'bg-white/10' : 'bg-[#111827]' }} text-white/90 rounded-2xl px-3 py-2 max-w-[85%] border border-white/10">
                            <div class="text-sm whitespace-pre-wrap">{{ $m->content }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Input --}}
            <div class="p-4 border-t border-white/10">
                <form wire:submit.prevent="send" class="flex gap-2">
                    <textarea wire:model.defer="message" rows="1"
                              class="w-full rounded-xl bg-black/50 border border-white/10 text-white/90 px-3 py-2 focus:outline-none"
                              placeholder="Напишите… (Enter — отправить, Shift+Enter — перенос)"
                              x-data
                              @keydown.enter.prevent="if(!$event.shiftKey){ $wire.send(); } else { $el.value += '\n' }"></textarea>

                    <button type="submit"
                            class="rounded-xl px-3 py-2 bg-white/10 border border-white/10 text-white/90 hover:bg-white/15">
                        ➤
                    </button>
                </form>
            </div>

        </div>
    </div>
</template>
