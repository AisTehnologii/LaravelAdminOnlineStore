<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;

use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $navigationLabel = 'Catalog';
    protected static ?int    $navigationSort  = 30;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label('Section')
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) =>
                            $query->where('type', 'catalog')->orderBy('position') // ✅ catalog
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Hidden::make('type')->default('catalog'), // ✅ catalog
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('slug')->required(),
                        Forms\Components\TextInput::make('position')->numeric()->default(1),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),

                Forms\Components\Select::make('locale')
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]),

            Forms\Components\Toggle::make('is_active')->default(true),

            Forms\Components\Section::make('Preview / Announce')->schema([
                Forms\Components\TextInput::make('announce_title')->label('Announce title')->maxLength(255),
                Forms\Components\Textarea::make('announce_description')->label('Announce description')->rows(3),
                Forms\Components\FileUpload::make('announce_image_path')
                    ->label('Announce image')
                    ->image()
                    ->directory('products/announce')
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Details')->schema([
                Forms\Components\TextInput::make('title')->label('Detail title')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->label('Detail description')->rows(5),
                Forms\Components\Textarea::make('description_extra')->label('Additional detail description')->rows(4),

                Forms\Components\Repeater::make('images')
                    ->relationship()
                    ->label('Detail images')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->directory('products/details')
                            ->required(),
                        Forms\Components\TextInput::make('position')->numeric()->default(1),
                    ])
                    ->orderColumn('position')
                    ->collapsed()
                    ->defaultItems(0),
            ])->columns(2),

            Forms\Components\Section::make('Price')->schema([
                Forms\Components\TextInput::make('price')->numeric()->label('Price (no discount)'),
                Forms\Components\TextInput::make('sale_price')->numeric()->label('Sale price'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'catalog'; // ✅ catalog

        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with('section')->orderBy('section_id')->orderBy('position')
            )
            ->groups([
                Group::make('section.title')->label('Section')->collapsible(),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\TextColumn::make('locale')->badge(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('position')->sortable(),
                Tables\Columns\ImageColumn::make('announce_image_path')->square()->label('Image'),
                Tables\Columns\TextColumn::make('title')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('sale_price')->label('Sale')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Price')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label('Section')
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) =>
                            $query->where('type', $type)->orderBy('position') // ✅ catalog
                    ),
                SelectFilter::make('locale')
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
