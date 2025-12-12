<x-filament::widget>
    <x-filament::card class="bg-black/40 border border-white/10 backdrop-blur-md">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-white">
                    Последние изменения контента
                </h2>
                <p class="text-xs text-white/70">
                    Что недавно редактировали в текстовых блоках
                </p>
            </div>
        </div>

        @php
            $records = $this->getRecords();
        @endphp

        @if($records->isEmpty())
            <p class="text-xs text-white/60">
                Изменений ещё не было.
            </p>
        @else
            <ul class="space-y-2">
                @foreach($records as $record)
                    <li class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2">
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-white">
                                {{ $record->section }} — {{ $record->key }}
                            </span>
                            <span class="text-[11px] text-white/60">
                                {{ $record->locale }} · {{ $record->updated_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                        <a
                            href="{{ route('filament.admin.resources.content-blocks.edit', $record) }}"
                            class="text-[11px] text-emerald-300 hover:text-emerald-200 underline"
                        >
                            Редактировать
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::card>
</x-filament::widget>
