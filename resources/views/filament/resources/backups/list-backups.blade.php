<x-filament-panels::page>
    <div class="glass-2 dash-card dash-hover" style="border-radius: 22px;">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-white/90">
                    {{ __('backup.page.title') }}
                </div>

                <div class="muted text-sm mt-1 leading-relaxed">
                    {{ __('backup.page.subtitle') }}
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="dash-pill">{{ __('backup.page.badges.env') }}</span>
                    <span class="dash-pill">{{ __('backup.page.badges.full') }}</span>
                </div>
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
