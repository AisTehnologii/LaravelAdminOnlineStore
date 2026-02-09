<x-filament::page>
    <div class="space-y-6">

        <x-filament::card>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><strong>{{ __('revision.view_page.date') }}:</strong> {{ $record->created_at->format('Y-m-d H:i') }}</div>
                <div><strong>{{ __('revision.view_page.event') }}:</strong> {{ strtoupper($record->event) }}</div>
                <div><strong>{{ __('revision.view_page.model') }}:</strong> {{ class_basename($record->revisionable_type) }}</div>
                <div><strong>{{ __('revision.table.id') }}:</strong> {{ $record->revisionable_id }}</div>
                <div><strong>{{ __('revision.view_page.user') }}:</strong> {{ $record->user->name ?? __('revision.common.dash') }}</div>
                <div><strong>{{ __('revision.view_page.ip') }}:</strong> {{ $record->ip }}</div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">{{ __('revision.view_page.changes') }}</h2>

            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">{{ __('revision.view_page.field') }}</th>
                        <th class="text-left py-2">{{ __('revision.view_page.was') }}</th>
                        <th class="text-left py-2">{{ __('revision.view_page.became') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($changes as $field => $diff)
                    <tr class="border-b align-top">
                        <td class="py-2 font-medium">{{ $field }}</td>

                        <td class="py-2 pr-4">
                            @if($diff['old'] !== null)
                                <span class="text-red-600 line-through">{{ $diff['old'] }}</span>
                            @else
                                <span class="text-gray-400">{{ __('revision.common.dash') }}</span>
                            @endif
                        </td>

                        <td class="py-2">
                            @if($diff['new'] !== null)
                                <span class="text-green-700 font-semibold">{{ $diff['new'] }}</span>
                            @else
                                <span class="text-gray-400">{{ __('revision.common.dash') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-filament::card>

    </div>
</x-filament::page>
