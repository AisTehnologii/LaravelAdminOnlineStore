<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('order.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('order.page.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('order.page.nav_label');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('number')
                    ->label(__('order.fields.number'))
                    ->disabled(),

                Forms\Components\TextInput::make('external_id')
                    ->label(__('order.fields.external_id'))
                    ->disabled(),

                Forms\Components\DateTimePicker::make('ordered_at')
                    ->label(__('order.fields.ordered_at'))
                    ->disabled(),
            ]),

            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('customer_name')
                    ->label(__('order.fields.customer_name'))
                    ->disabled(),

                Forms\Components\TextInput::make('customer_phone')
                    ->label(__('order.fields.customer_phone'))
                    ->disabled(),

                Forms\Components\TextInput::make('customer_email')
                    ->label(__('order.fields.customer_email'))
                    ->disabled(),
            ]),

            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('grand_total')
                    ->label(__('order.fields.grand_total'))
                    ->disabled(),

                Forms\Components\TextInput::make('currency')
                    ->label(__('order.fields.currency'))
                    ->disabled(),

                Forms\Components\TextInput::make('status')
                    ->label(__('order.fields.status'))
                    ->disabled(),
            ]),

            Forms\Components\Textarea::make('raw')
                ->label(__('order.fields.raw'))
                ->disabled()
                ->rows(8),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->latest('ordered_at')->with(['user', 'items.product']))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('order.fields.id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('number')
                    ->label(__('order.fields.number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('external_id')
                    ->label(__('order.fields.external_id'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('ordered_at')
                    ->label(__('order.fields.ordered_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label(__('order.fields.customer_name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label(__('order.fields.customer_phone'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_email')
                    ->label(__('order.fields.customer_email'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label(__('order.fields.grand_total'))
                    ->money(fn ($record) => $record->currency ?: 'MDL', locale: app()->getLocale())
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency')
                    ->label(__('order.fields.currency'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('order.fields.status'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('order.fields.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('order.filters.status'))
                    ->options([
                        'new'       => __('order.statuses.new'),
                        'paid'      => __('order.statuses.paid'),
                        'done'      => __('order.statuses.done'),
                        'cancelled' => __('order.statuses.cancelled'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('order.actions.view')),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
