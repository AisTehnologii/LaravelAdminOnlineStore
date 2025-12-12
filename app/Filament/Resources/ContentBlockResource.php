<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentBlockResource\Pages;
use App\Models\ContentBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ContentBlockResource extends Resource
{
    protected static ?string $model = ContentBlock::class;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Content Blocks';
    protected static ?int    $navigationSort  = 50;

    /*
     |--------------------------------------------------------------------------
     | Справочники секций и полей
     |--------------------------------------------------------------------------
     */

    /**
     * Человеческие названия секций.
     */
    public static function sectionLabels(): array
    {
        return [
            'home.about_card' => 'Главная — блок About (карточка рядом со слайдером)',
            'home.blog_intro' => 'Главная — блок «Blog & Updates» (заголовок и описание)',
            'layout.header'   => 'Шапка сайта (меню, логотип, кнопка)',
            'home.sections'   => 'Главная — заголовки секций (What we do, Our projects)',
            'layout.footer'   => 'Футер (About, Features, подписка, соцсети, копирайт)',
        ];
    }

    /**
     * Справочник: для каждой секции — список полей (ключ => название).
     * Здесь только то, что реально используем в шаблоне.
     */
   public static function fieldsBySection(): array
{
    return [

        // 1) About Card на главной
        'home.about_card' => [
            'home.about_card.title'  => 'Заголовок карточки (H2)',
            'home.about_card.text_1' => 'Первый абзац текста',
            'home.about_card.text_2' => 'Второй абзац текста',
            'home.about_card.name'   => 'Имя (подпись под текстом)',
            'home.about_card.role'   => 'Роль / должность под именем',
        ],

        // 2) Вступление к блоку Blog & Updates
        'home.blog_intro' => [
            'title'            => 'Заголовок блока «Blog & Updates»',
            'text'             => 'Текст/описание под заголовком',
            'read_more_label'  => 'Текст ссылки на карточках блога (Read more)',
        ],

        // 3) Header (шапка сайта) — только подписи, ссылки жёстко как якоря
        'layout.header' => [
            'logo_text'                 => 'Текст логотипа по центру',

            'menu_left_home'            => 'Левое меню — Home (подпись, якорь #home-section)',
            'menu_left_projects'        => 'Левое меню — Projects (подпись, якорь #projects-section)',
            'menu_left_services'        => 'Левое меню — Services (подпись, якорь #services-section)',

            'menu_right_about'          => 'Правое меню — About (подпись, якорь #about-section)',
            'menu_right_blog'           => 'Правое меню — Blog (подпись, якорь #blog-section)',
            'menu_right_contact'        => 'Правое меню — Contact (подпись, якорь #contact-section)',

            'hero_button_contact'       => 'Кнопка на баннере (текст, ведёт к #contact-section)',
        ],

        // 4) Заголовки секций на главной
        'home.sections' => [
            'what_we_do_title'      => 'Блок «What We Do» — заголовок',
            'our_projects_title'    => 'Блок «Our Projects» — заголовок',
            'our_projects_view_all' => '«View All Projects» — текст ссылки',
        ],

        // 5) Футер
        'layout.footer' => [
            'about_title'              => 'Футер — блок About Us (заголовок)',
            'about_text'               => 'Футер — блок About Us (текст)',

            'features_title'           => 'Футер — блок Features (заголовок)',
            'features_link_about'      => 'Features — пункт About Us (подпись, якорь #about-section)',
            'features_link_testimonials'     => 'Features — пункт Testimonials (подпись, якорь #testimonials-section)',
            'features_link_terms'            => 'Features — пункт Terms of Service (подпись, пока без ссылки)',
            'features_link_privacy'          => 'Features — пункт Privacy (подпись, пока без ссылки)',
            'features_link_contact'          => 'Features — пункт Contact Us (подпись, якорь #contact-section)',

            'newsletter_title'        => 'Подписка — заголовок',
            'newsletter_placeholder'  => 'Подписка — placeholder в поле Email',
            'newsletter_button'       => 'Подписка — текст кнопки',

            'follow_us_title'         => 'Соцсети — заголовок',

            'social_facebook_url'     => 'Ссылка на Facebook',
            'social_twitter_url'      => 'Ссылка на Twitter',
            'social_instagram_url'    => 'Ссылка на Instagram',
            'social_linkedin_url'     => 'Ссылка на LinkedIn',

            'copyright_text'          => 'Копирайт (низ футера, можно с HTML)',
        ],
    ];
}


    /**
     * Плоский словарь ключ => человекочитаемое название (для таблицы).
     */
    public static function fieldLabels(): array
    {
        $result = [];

        foreach (self::fieldsBySection() as $section => $fields) {
            foreach ($fields as $key => $label) {
                $result[$key] = $label;
                // на всякий случай дублируем вариант section.key
                $result[$section . '.' . $key] = $label;
            }
        }

        return $result;
    }

    /**
     * Опции полей для конкретной секции (для селекта в форме).
     */
    public static function fieldOptionsForSection(?string $section): array
    {
        $all = self::fieldsBySection();

        return $all[$section] ?? [];
    }

    /*
     |--------------------------------------------------------------------------
     | Форма
     |--------------------------------------------------------------------------
     */

    public static function form(Form $form): Form
    {
        $sectionLabels = self::sectionLabels();

        return $form
            ->schema([
                Forms\Components\Section::make('Где используется текст')
                    ->description('Выберите участок сайта и конкретный элемент (заголовок, текст, ссылка и т.п.), к которому относится этот контент.')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Select::make('section')
                                ->label('Раздел сайта')
                                ->options($sectionLabels)
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->helperText('Например: «Главная — блок About», «Шапка сайта», «Футер».'),

                            Forms\Components\Select::make('key')
                                ->label('Элемент внутри раздела')
                                ->options(fn (Get $get) => self::fieldOptionsForSection($get('section')))
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->helperText('Что именно редактируете: заголовок, текст, подпись, ссылку и т.д.'),

                            Forms\Components\Select::make('locale')
                                ->label('Язык')
                                ->options([
                                    'en' => 'English',
                                    'ru' => 'Русский',
                                    'ro' => 'Română',
                                ])
                                ->default('en')
                                ->required()
                                ->helperText('Для какого языка этот текст.'),
                        ]),

                        Forms\Components\Placeholder::make('field_hint')
                            ->label('Подсказка по полю')
                            ->content(function (Get $get) {
                                $section = $get('section');
                                $key     = $get('key');

                                if (! $section || ! $key) {
                                    return 'Сначала выберите раздел и элемент — здесь появится пояснение, где и как это используется на сайте.';
                                }

                                $options = self::fieldOptionsForSection($section);

                                return $options[$key]
                                    ?? 'Элемент будет использоваться на сайте согласно верстке. Убедитесь, что выбрали правильный ключ.';
                            }),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Содержимое')
                    ->description('Этот текст (или HTML) будет напрямую выводиться на сайте через {!! !!}. Будьте аккуратны с разметкой.')
                    ->schema([
                        Forms\Components\RichEditor::make('value')
                            ->label('Контент')
                            ->helperText('Можно использовать абзацы, списки, ссылки, жирный текст и т.д. Если поле — ссылка (URL), лучше вставить чистый адрес без HTML.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /*
     |--------------------------------------------------------------------------
     | Таблица
     |--------------------------------------------------------------------------
     */

    public static function table(Table $table): Table
{
    $sectionLabels = self::sectionLabels();
    $fieldLabels   = self::fieldLabels();

    return $table
        ->columns([
            Tables\Columns\TextColumn::make('section')
                ->label('Раздел')
                ->formatStateUsing(fn (string $state) => $sectionLabels[$state] ?? $state)
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('key')
                ->label('Элемент')
                ->formatStateUsing(function ($state, ContentBlock $record) use ($fieldLabels) {
                    if (isset($fieldLabels[$state])) {
                        return $fieldLabels[$state];
                    }

                    $compound = $record->section . '.' . $state;

                    return $fieldLabels[$compound] ?? $state;
                })
                ->wrap()
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('locale')
                ->label('Язык')
                ->sortable()
                ->badge(),

            Tables\Columns\TextColumn::make('value')
                ->label('Превью контента')
                ->limit(60)
                ->html()
                ->toggleable(),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Обновлён')
                ->dateTime('Y-m-d H:i')
                ->sortable(),
        ])
        ->groups([
            Group::make('section')
                ->label('Раздел')
                ->getTitleFromRecordUsing(
                    fn (ContentBlock $record) =>
                        $sectionLabels[$record->section] ?? $record->section
                )
                ->collapsible(),
        ])
        ->defaultGroup('section')
         // 🔹 вот эта строка сворачивает все группы по умолчанию
        ->filters([
            SelectFilter::make('section')
                ->label('Раздел')
                ->options($sectionLabels),

            SelectFilter::make('locale')
                ->label('Язык')
                ->options([
                    'en' => 'English',
                    'ru' => 'Русский',
                    'ro' => 'Română',
                ]),
        ])
        ->actions([
            Tables\Actions\EditAction::make()
                ->label('Edit'),
            Tables\Actions\DeleteAction::make()
                ->label('Delete'),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}


    /*
     |--------------------------------------------------------------------------
     | Страницы
     |--------------------------------------------------------------------------
     */

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContentBlocks::route('/'),
            'create' => Pages\CreateContentBlock::route('/create'),
            'edit'   => Pages\EditContentBlock::route('/{record}/edit'),
        ];
    }
}
