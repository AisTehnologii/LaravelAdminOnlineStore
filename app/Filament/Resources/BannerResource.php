<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use App\Models\ContentSection;
use App\Support\TableExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use App\Filament\Resources\RelationManagers\RevisionsRelationManager;


class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Banners';
    protected static ?int    $navigationSort  = 10;

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
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'banner')->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Hidden::make('type')->default('banner'),
                        Forms\Components\TextInput::make('title')->required()->maxLength(255),
                        Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                        Forms\Components\TextInput::make('position')->numeric()->default(1),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),

                Forms\Components\Select::make('locale')
                    ->label('Locale')
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label('Position')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]),

            Forms\Components\TextInput::make('title')->required()->maxLength(255),

            Forms\Components\Textarea::make('text')->rows(3)->nullable(),

            Forms\Components\FileUpload::make('image_path')
                ->label('Image')
                ->image()
                ->directory('banners')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'banner';

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('section')
                ->orderBy('section_id')
                ->orderBy('position')
            )
            ->groups([
                Group::make('section.title')
                    ->label('Section')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Экспорт данных')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading('Экспорт данных')
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
                            ->options(fn () => ContentSection::query()
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
                            'updated_at'    => 'Updated at',
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : Banner::query();

                        $query->with('section')->orderBy('section_id')->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && !empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = 'Banners export';
                        $subtitle = now()->format('Y-m-d H:i');

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="banners_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', [
                            'title'    => $title,
                            'subtitle' => $subtitle,
                            'columns'  => $columns,
                            'rows'     => $rows,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'banners_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('locale')->badge()->sortable(),
                Tables\Columns\TextColumn::make('position')->sortable(),
                Tables\Columns\ImageColumn::make('image_path')->square()->label('Image'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label('Section')
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', $type)->orderBy('position')
                    ),
                SelectFilter::make('locale')
                    ->label('Locale')
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit'   => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
