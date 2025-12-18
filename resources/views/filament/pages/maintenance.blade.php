<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-4">
        {{ $this->form }}

        <x-filament::button type="submit" color="warning" icon="heroicon-o-check">
            Сохранить
        </x-filament::button>
    </form>
</x-filament-panels::page>
