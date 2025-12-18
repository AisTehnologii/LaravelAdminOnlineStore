<x-filament::page>
    <div class="space-y-6">

        {{-- Общая информация --}}
        <x-filament::card>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><strong>Дата:</strong> {{ $record->created_at->format('Y-m-d H:i') }}</div>
                <div><strong>Событие:</strong> {{ strtoupper($record->event) }}</div>
                <div><strong>Модель:</strong> {{ class_basename($record->revisionable_type) }}</div>
                <div><strong>ID:</strong> {{ $record->revisionable_id }}</div>
                <div><strong>Пользователь:</strong> {{ $record->user->name ?? '—' }}</div>
                <div><strong>IP:</strong> {{ $record->ip }}</div>
            </div>
        </x-filament::card>

        {{-- Изменения --}}
        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">Изменения</h2>

            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Поле</th>
                        <th class="text-left py-2">Было</th>
                        <th class="text-left py-2">Стало</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($changes as $field => $diff)
                    <tr class="border-b align-top">
                        <td class="py-2 font-medium">{{ $field }}</td>

                        {{-- OLD --}}
                        <td class="py-2 pr-4">
                            @if($diff['old'] !== null)
                                <span class="text-red-600 line-through">
                                    {{ $diff['old'] }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- NEW --}}
                        <td class="py-2">
                            @if($diff['new'] !== null)
                                <span class="text-green-700 font-semibold">
                                    {{ $diff['new'] }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-filament::card>

    </div>
</x-filament::page>
