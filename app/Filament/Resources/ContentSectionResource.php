<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentSectionResource\Pages;
use App\Models\ContentSection;
use App\Support\TableExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ContentSectionResource extends Resource
{
    protected static ?string $model = ContentSection::class;

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->required()
                ->options([
                    'banner'    => 'Banners',
                    'slider'    => 'Sliders',
                    'card'      => 'Cards',
                    'project'   => 'Projects',
                    'quote'     => 'Quotes',
                    'blog_card' => 'Blog Cards',
                    'promo'     => 'Promo Blocks',
                    'catalog'   => 'Catalog', // ✅ ДОБАВИЛИ
                ]),

            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('position')->numeric()->default(1)->required(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Экспорт данных')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading('Экспорт данных')
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label('Что экспортировать?')
                            ->options([
                                'all'  => 'Вся таблица (все типы)',
                                'type' => 'Только выбранный тип',
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('type')
                            ->label('Тип')
                            ->options([
                                'banner'    => 'Banners',
                                'slider'    => 'Sliders',
                                'card'      => 'Cards',
                                'project'   => 'Projects',
                                'quote'     => 'Quotes',
                                'blog_card' => 'Blog Cards',
                                'promo'     => 'Promo Blocks',
                                'catalog'   => 'Catalog', // ✅ ДОБАВИЛИ
                            ])
                            ->visible(fn (callable $get) => $get('scope') === 'type')
                            ->required(fn (callable $get) => $get('scope') === 'type'),

                        Forms\Components\Select::make('format')
                            ->label('Формат')
                            ->options(['pdf' => 'PDF', 'xml' => 'XML'])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $columns = [
                            'type'      => 'Type',
                            'position'  => 'Position',
                            'title'     => 'Title',
                            'slug'      => 'Slug',
                            'is_active' => 'Is active',
                            'updated_at'=> 'Updated at',
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : ContentSection::query();

                        if (($data['scope'] ?? 'all') === 'type' && !empty($data['type'])) {
                            $query->where('type', $data['type']);
                        }

                        $rows = $query->orderBy('type')->orderBy('position')->get();

                        $title = 'Content sections export';
                        $subtitle = now()->format('Y-m-d H:i');

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="content_sections_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'content_sections_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('position')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'banner'    => 'Banners',
                        'slider'    => 'Sliders',
                        'card'      => 'Cards',
                        'project'   => 'Projects',
                        'quote'     => 'Quotes',
                        'blog_card' => 'Blog Cards',
                        'promo'     => 'Promo Blocks',
                        'catalog'   => 'Catalog', // ✅ ДОБАВИЛИ
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContentSections::route('/'),
            'create' => Pages\CreateContentSection::route('/create'),
            'edit'   => Pages\EditContentSection::route('/{record}/edit'),
        ];
    }
}
