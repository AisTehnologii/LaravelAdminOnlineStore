<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoBlockResource\Pages;
use App\Models\PromoBlock;
use App\Models\ContentSection;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use App\Support\TableExport;
use Barryvdh\DomPDF\Facade\Pdf;


class PromoBlockResource extends Resource
{
    protected static ?string $model = PromoBlock::class;

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Promo Blocks';
    protected static ?int    $navigationSort  = 20;

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
                            $query->where('type', 'promo')->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Hidden::make('type')->default('promo'),
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('slug')->required(),
                        Forms\Components\TextInput::make('position')->numeric()->default(1),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),

                Forms\Components\Select::make('locale')
                    ->options([
                        'en' => 'English',
                        'ru' => 'Русский',
                        'ro' => 'Română',
                    ])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]),

            Forms\Components\TextInput::make('title')
                ->label('Main title')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('subtitle')
                ->label('Subtitle')
                ->maxLength(255),

            Forms\Components\Textarea::make('description')->rows(3),
            Forms\Components\Textarea::make('description_2')->rows(2),
            Forms\Components\Textarea::make('description_3')->rows(2),

            Forms\Components\TextInput::make('link')
                ->label('Link')
                ->url()
                ->nullable(),

            Forms\Components\FileUpload::make('image_path')
                ->label('Image')
                ->image()
                ->directory('promo-blocks')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
{
    $type = 'promo';

    return $table
        ->modifyQueryUsing(fn (Builder $query) =>
            $query->with('section')
                  ->orderBy('section_id')
                  ->orderBy('position')
        )
        ->groups([
            Group::make('section.title')
                ->label('Section')
                ->collapsible(),
        ])
        ->defaultGroup('section.title')
        ->defaultSort('position')

        // ✅ HEADER EXPORT (как в banners)
        ->headerActions([
            Tables\Actions\Action::make('export')
                ->label('Экспорт данных')
                ->icon('heroicon-o-arrow-down-tray')
                ->modalHeading('Экспорт Promo Blocks')
                ->form([
                    Forms\Components\Select::make('scope')
                        ->label('Что экспортировать?')
                        ->options([
                            'all'     => 'Все секции',
                            'section' => 'Только выбранную секцию',
                        ])
                        ->default('all')
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('section_id')
                        ->label('Секция')
                        ->options(fn () =>
                            ContentSection::query()
                                ->where('type', $type)
                                ->orderBy('position')
                                ->pluck('title', 'id')
                                ->toArray()
                        )
                        ->visible(fn (callable $get) => $get('scope') === 'section')
                        ->searchable(),

                    Forms\Components\Select::make('format')
                        ->label('Формат')
                        ->options([
                            'pdf' => 'PDF',
                            'xml' => 'XML',
                        ])
                        ->default('pdf')
                        ->required(),
                ])
                ->action(function (array $data, $livewire) use ($type) {

                    $columns = [
                        'section.title' => 'Section',
                        'locale'        => 'Locale',
                        'position'      => 'Position',
                        'title'         => 'Title',
                        'subtitle'      => 'Subtitle',
                        'updated_at'    => 'Updated at',
                    ];

                    $query = method_exists($livewire, 'getFilteredTableQuery')
                        ? $livewire->getFilteredTableQuery()
                        : PromoBlock::query();

                    $query->with('section')
                          ->orderBy('section_id')
                          ->orderBy('position');

                    if (($data['scope'] ?? 'all') === 'section' && !empty($data['section_id'])) {
                        $query->where('section_id', $data['section_id']);
                    }

                    $rows = $query->get();

                    $title = 'Promo Blocks export';
                    $subtitle = now()->format('Y-m-d H:i');

                    // XML
                    if (($data['format'] ?? 'pdf') === 'xml') {
                        $xml = TableExport::toXml('export', 'row', $columns, $rows);

                        return response($xml, 200, [
                            'Content-Type'        => 'application/xml; charset=UTF-8',
                            'Content-Disposition' => 'attachment; filename="promo_blocks_' . now()->format('Ymd_His') . '.xml"',
                        ]);
                    }

                    // PDF
                    $pdf = Pdf::loadView('exports.table-pdf', [
                        'title'    => $title,
                        'subtitle' => $subtitle,
                        'columns'  => $columns,
                        'rows'     => $rows,
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'promo_blocks_' . now()->format('Ymd_His') . '.pdf'
                    );
                }),
        ])

        // ✅ COLUMNS
        ->columns([
            Tables\Columns\TextColumn::make('locale')->badge(),
            Tables\Columns\TextColumn::make('position')->sortable(),
            Tables\Columns\ImageColumn::make('image_path')->square()->label('Image'),
            Tables\Columns\TextColumn::make('title')->limit(40)->searchable(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime('Y-m-d H:i')->sortable(),
        ])

        // ✅ FILTERS
        ->filters([
            SelectFilter::make('section_id')
                ->label('Section')
                ->relationship(
                    name: 'section',
                    titleAttribute: 'title',
                    modifyQueryUsing: fn (Builder $query) =>
                        $query->where('type', $type)->orderBy('position')
                ),
            SelectFilter::make('locale')
                ->options([
                    'en' => 'English',
                    'ru' => 'Русский',
                    'ro' => 'Română',
                ]),
        ])

        // ✅ ACTIONS
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
            'index'  => Pages\ListPromoBlocks::route('/'),
            'create' => Pages\CreatePromoBlock::route('/create'),
            'edit'   => Pages\EditPromoBlock::route('/{record}/edit'),
        ];
    }
}
