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
    protected static ?int    $navigationSort  = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    // ✅ меню
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('content_section.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('content_section.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('content_section.page.nav_label');
    }

    protected static function typeOptions(): array
    {
        return [
            'banner'    => __('content_section.types.banner'),
            'slider'    => __('content_section.types.slider'),
            'card'      => __('content_section.types.card'),
            'project'   => __('content_section.types.project'),
            'quote'     => __('content_section.types.quote'),
            'blog_card' => __('content_section.types.blog_card'),
            'promo'     => __('content_section.types.promo'),
            'catalog'   => __('content_section.types.catalog'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label(__('content_section.form.type'))
                ->required()
                ->options(static::typeOptions()),

            Forms\Components\TextInput::make('title')
                ->label(__('content_section.form.title'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('slug')
                ->label(__('content_section.form.slug'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('position')
                ->label(__('content_section.form.position'))
                ->numeric()
                ->default(1)
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label(__('content_section.form.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('content_section.actions.export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('content_section.export.modal_heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(__('content_section.export.scope'))
                            ->options([
                                'all'  => __('content_section.export.scope_all'),
                                'type' => __('content_section.export.scope_type'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('type')
                            ->label(__('content_section.export.type'))
                            ->options(static::typeOptions())
                            ->visible(fn (callable $get) => $get('scope') === 'type')
                            ->required(fn (callable $get) => $get('scope') === 'type'),

                        Forms\Components\Select::make('format')
                            ->label(__('content_section.export.format'))
                            ->options([
                                'pdf' => __('content_section.export.pdf'),
                                'xml' => __('content_section.export.xml'),
                            ])
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

                        if (($data['scope'] ?? 'all') === 'type' && ! empty($data['type'])) {
                            $query->where('type', $data['type']);
                        }

                        $rows = $query->orderBy('type')->orderBy('position')->get();

                        $title = __('content_section.export.title');
                        $subtitle = now()->format('Y-m-d H:i');

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="' . __('content_section.export.file_name') . '_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            __('content_section.export.file_name') . '_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('content_section.table.type'))
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => static::typeOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('content_section.table.position'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('content_section.table.title'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('content_section.table.slug'))
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('content_section.table.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('content_section.filters.type'))
                    ->options(static::typeOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
