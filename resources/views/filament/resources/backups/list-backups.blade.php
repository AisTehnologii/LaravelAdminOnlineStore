<x-filament-panels::page>
    <div class="glass-2 dash-card dash-hover" style="border-radius: 22px;">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-white/90">📦 Backups</div>
                <div class="muted text-sm mt-1 leading-relaxed">
                    Здесь хранятся ZIP-бэкапы проекта. Можно создать новый и скачать любой из списка.
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="dash-pill">✅ .env включается</span>
                    <span class="dash-pill">📦 FULL backup (все папки)</span></div>
            </div>

            {{-- Кнопки headerActions Filament --}}
            <div class="shrink-0">
    <x-filament::actions :actions="$this->getCachedHeaderActions()" />
</div>

        </div>

        <div class="mt-6">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
