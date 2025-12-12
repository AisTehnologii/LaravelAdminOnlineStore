<x-filament::widget>
    <x-filament::card class="bg-black/40 border border-white/10 backdrop-blur-md">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">
                Быстрые действия
            </h2>
        </div>

        <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('filament.admin.resources.banners.create') }}"
               class="group flex items-center gap-3 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-3 py-3 text-sm text-white hover:bg-emerald-500/20 transition">
                <x-filament::icon
                    icon="heroicon-o-photo"
                    class="h-5 w-5 text-emerald-300 group-hover:scale-110 transition"
                />
                <div class="flex flex-col">
                    <span class="font-medium">Добавить баннер</span>
                    <span class="text-[11px] text-emerald-100/80">Hero-слайдер на главной</span>
                </div>
            </a>

            <a href="{{ route('filament.admin.resources.projects.create') }}"
               class="group flex items-center gap-3 rounded-xl border border-sky-500/40 bg-sky-500/10 px-3 py-3 text-sm text-white hover:bg-sky-500/20 transition">
                <x-filament::icon
                    icon="heroicon-o-rectangle-stack"
                    class="h-5 w-5 text-sky-300 group-hover:scale-110 transition"
                />
                <div class="flex flex-col">
                    <span class="font-medium">Добавить проект</span>
                    <span class="text-[11px] text-sky-100/80">Портфолио / кейсы</span>
                </div>
            </a>

            <a href="{{ route('filament.admin.resources.content-blocks.index') }}"
               class="group flex items-center gap-3 rounded-xl border border-amber-500/40 bg-amber-500/10 px-3 py-3 text-sm text-white hover:bg-amber-500/20 transition">
                <x-filament::icon
                    icon="heroicon-o-document-text"
                    class="h-5 w-5 text-amber-300 group-hover:scale-110 transition"
                />
                <div class="flex flex-col">
                    <span class="font-medium">Тексты сайта</span>
                    <span class="text-[11px] text-amber-100/80">Header, footer, блог, about…</span>
                </div>
            </a>

            <a href="{{ route('filament.admin.resources.blog-cards.index') }}"
               class="group flex items-center gap-3 rounded-xl border border-fuchsia-500/40 bg-fuchsia-500/10 px-3 py-3 text-sm text-white hover:bg-fuchsia-500/20 transition">
                <x-filament::icon
                    icon="heroicon-o-newspaper"
                    class="h-5 w-5 text-fuchsia-300 group-hover:scale-110 transition"
                />
                <div class="flex flex-col">
                    <span class="font-medium">Блог / новости</span>
                    <span class="text-[11px] text-fuchsia-100/80">Карточки блока Blog & Updates</span>
                </div>
            </a>
        </div>
    </x-filament::card>
</x-filament::widget>
