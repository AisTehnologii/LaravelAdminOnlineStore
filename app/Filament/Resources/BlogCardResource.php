<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCardResource\Pages;
use App\Models\BlogCard;
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

class BlogCardResource extends Resource
{
    protected static ?string $model = BlogCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?int $navigationSort = 60;

    // ✅ меню
    public static function getNavigationGroup(): ?string
    {
        return __('nav.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('blog_card.page.nav_label');
    }

    public static function getLabel(): ?string
    {
        return __('blog_card.page.nav_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('blog_card.page.nav_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        $type = 'blog_card';

        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('section_id')
                    ->label(__('blog_card.form.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('type', $type)
                            ->orderBy('position')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('locale')
                    ->label(__('blog_card.form.locale'))
                    ->options(['en' => 'English', 'ru' => 'Русский', 'ro' => 'Română'])
                    ->default('en')
                    ->required(),

                Forms\Components\TextInput::make('position')
                    ->label(__('blog_card.form.position'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]),

            Forms\Components\DatePicker::make('date')
                ->label(__('blog_card.form.date'))
                ->nullable(),

            Forms\Components\TextInput::make('title')
                ->label(__('blog_card.form.title'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('url')
                ->label(__('blog_card.form.url'))
                ->maxLength(255)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $type = 'blog_card';

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('section')
                ->orderBy('section_id')
                ->orderBy('position')
            )
            ->groups([
                Group::make('section.title')
                    ->label(__('blog_card.form.section'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('section.title')
            ->defaultSort('position')

            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('blog_card.export.action'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('blog_card.export.heading'))
                    ->form([
                        Forms\Components\Select::make('scope')
                            ->label(__('blog_card.export.scope'))
                            ->options([
                                'all'     => __('blog_card.export.scope_all'),
                                'section' => __('blog_card.export.scope_section'),
                            ])
                            ->default('all')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section_id')
                            ->label(__('blog_card.export.section'))
                            ->options(fn () => ContentSection::query()
                                ->where('type', $type)
                                ->orderBy('position')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->visible(fn (callable $get) => $get('scope') === 'section')
                            ->searchable(),

                        Forms\Components\Select::make('format')
                            ->label(__('blog_card.export.format'))
                            ->options([
                                'pdf' => __('blog_card.export.pdf'),
                                'xml' => __('blog_card.export.xml'),
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) use ($type) {
                        $columns = [
                            'section.title' => __('blog_card.export.columns.section'),
                            'locale'        => __('blog_card.export.columns.locale'),
                            'position'      => __('blog_card.export.columns.position'),
                            'date'          => __('blog_card.export.columns.date'),
                            'title'         => __('blog_card.export.columns.title'),
                            'url'           => __('blog_card.export.columns.url'),
                            'updated_at'    => __('blog_card.export.columns.updated'),
                        ];

                        $query = method_exists($livewire, 'getFilteredTableQuery')
                            ? $livewire->getFilteredTableQuery()
                            : BlogCard::query();

                        $query->with('section')->orderBy('section_id')->orderBy('position');

                        if (($data['scope'] ?? 'all') === 'section' && ! empty($data['section_id'])) {
                            $query->where('section_id', $data['section_id']);
                        }

                        $rows = $query->get();

                        $title = __('blog_card.export.title');
                        $subtitle = now()->format(__('blog_card.export.subtitle_fmt'));

                        if (($data['format'] ?? 'pdf') === 'xml') {
                            $xml = TableExport::toXml('export', 'row', $columns, $rows);

                            return response($xml, 200, [
                                'Content-Type'        => 'application/xml; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename="blog_cards_' . now()->format('Ymd_His') . '.xml"',
                            ]);
                        }

                        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'subtitle', 'columns', 'rows'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'blog_cards_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('blog_card.table.locale'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('blog_card.table.position'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('blog_card.table.date'))
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('blog_card.table.title'))
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('url')
                    ->label(__('blog_card.table.url'))
                    ->toggleable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('blog_card.table.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section_id')
                    ->label(__('blog_card.filters.section'))
                    ->relationship(
                        name: 'section',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('type', $type)
                            ->orderBy('position')
                    ),

                SelectFilter::make('locale')
                    ->label(__('blog_card.filters.locale'))
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
            'index'  => Pages\ListBlogCards::route('/'),
            'create' => Pages\CreateBlogCard::route('/create'),
            'edit'   => Pages\EditBlogCard::route('/{record}/edit'),
        ];
    }
}
