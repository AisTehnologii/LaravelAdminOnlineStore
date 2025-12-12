<x-filament::widget>
    <x-filament::card
        class="bg-black/30 border border-amber-500/30 backdrop-blur-md"
    >
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-white">
                    Состояние контента
                </h2>
                <p class="text-xs text-white/70">
                    Заполненность текстовых блоков по языкам
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach($this->data['locales'] as $locale)
                @php
                    $percent = $this->data['completion'][$locale] ?? 0;
                @endphp
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-white uppercase">
                            {{ $locale }}
                        </span>
                        <span class="text-xs text-white/70">
                            {{ $percent }}%
                        </span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-white/10">
                        <div
                            class="h-full rounded-full bg-emerald-400 transition-all"
                            style="width: {{ $percent }}%;"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 border-t border-white/10 pt-4">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-white/60">
                Критичные пробелы
            </p>

            @php
                $sections = $this->data['sections'];
                $status   = $this->data['status'];
                $problems = [];
                foreach ($status as $locale => $sectionsData) {
                    foreach ($sectionsData as $sectionKey => $fields) {
                        foreach ($fields as $fieldKey => $ok) {
                            if (! $ok) {
                                $problems[] = [
                                    'locale'  => $locale,
                                    'section' => $sections[$sectionKey] ?? $sectionKey,
                                ];
                            }
                        }
                    }
                }
                $problems = collect($problems)->unique();
            @endphp

            @if ($problems->isEmpty())
                <p class="text-xs text-emerald-300">
                    Все основные тексты заполнены во всех языках 🎉
                </p>
            @else
                <ul class="space-y-1">
                    @foreach($problems->take(6) as $problem)
                        <li class="flex items-center gap-2 text-xs text-amber-200">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                            <span class="uppercase text-[10px] text-amber-300">
                                {{ $problem['locale'] }}
                            </span>
                            <span class="text-xs text-amber-100">
                                — заполните блок: {{ $problem['section'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                @if ($problems->count() > 6)
                    <p class="mt-2 text-[10px] text-white/50">
                        И ещё {{ $problems->count() - 6 }} незаполненных полей…
                    </p>
                @endif
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
