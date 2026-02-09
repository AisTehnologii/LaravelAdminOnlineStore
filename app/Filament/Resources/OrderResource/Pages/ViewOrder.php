<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make(__('order.view.sections.order'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('number')
                            ->label(__('order.fields.number'))
                            ->copyable(),

                        TextEntry::make('external_id')
                            ->label(__('order.fields.external_id'))
                            ->copyable()
                            ->placeholder(__('common.dash')),

                        TextEntry::make('ordered_at')
                            ->label(__('order.fields.ordered_at'))
                            ->dateTime('d.m.Y H:i'),

                        TextEntry::make('status')
                            ->label(__('order.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn ($state) => __('order.statuses.' . ($state ?: 'new'))),

                        TextEntry::make('currency')
                            ->label(__('order.fields.currency'))
                            ->badge()
                            ->placeholder('MDL'),

                        TextEntry::make('grand_total')
                            ->label(__('order.fields.grand_total'))
                            ->money(fn ($record) => $record->currency ?: 'MDL', locale: app()->getLocale()),
                    ]),

                Section::make(__('order.view.sections.customer'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('customer_name')
                            ->label(__('order.fields.customer_name'))
                            ->placeholder(fn ($record) => $record->user?->name ?: __('common.dash')),

                        TextEntry::make('customer_phone')
                            ->label(__('order.fields.customer_phone'))
                            ->placeholder(__('common.dash')),

                        TextEntry::make('customer_email')
                            ->label(__('order.fields.customer_email'))
                            ->placeholder(fn ($record) => $record->user?->email ?: __('common.dash')),
                    ]),

                Section::make(__('order.view.sections.items'))
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label(false)
                            ->columns(4)
                            ->schema([
                                TextEntry::make('product.title')
                                    ->label(__('order.item_fields.product'))
                                    ->formatStateUsing(function ($state, $record) {
                                        // 1) из products
                                        if ($record->product?->title) {
                                            return $record->product->title;
                                        }

                                        // 2) fallback: из raw->cart[product_id]->title
                                        $rawTitle = data_get(
                                            $record->order?->raw,
                                            'cart.' . (string) $record->product_id . '.title'
                                        );

                                        return $rawTitle ?: __('order.view.fallback.product', ['id' => $record->product_id]);
                                    }),

                                TextEntry::make('quantity')
                                    ->label(__('order.item_fields.quantity'))
                                    ->formatStateUsing(fn ($state) => number_format((float) $state, 3, '.', ' ')),

                                TextEntry::make('unit_amount')
                                    ->label(__('order.item_fields.unit_amount'))
                                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', ' '))
                                    ->suffix(fn ($record) => ' ' . ($record->order?->currency ?: 'MDL')),

                                TextEntry::make('total_amount')
                                    ->label(__('order.item_fields.total_amount'))
                                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', ' '))
                                    ->suffix(fn ($record) => ' ' . ($record->order?->currency ?: 'MDL')),
                            ])
                            ->visible(fn ($record) => $record->items?->count() > 0),
                    ]),

                Section::make(__('order.view.sections.raw'))
                    ->collapsed()
                    ->schema([
                        TextEntry::make('raw')
                            ->label(false)
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))
                            ->extraAttributes(['class' => 'whitespace-pre-wrap font-mono text-sm']),
                    ]),
            ]);
    }
}
