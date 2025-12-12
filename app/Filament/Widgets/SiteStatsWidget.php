<?php

namespace App\Filament\Widgets;

use App\Models\Banner;
use App\Models\BlogCard;
use App\Models\Card;
use App\Models\ContentBlock;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Slider;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStatsWidget extends BaseWidget
{
    /**
     * Автообновление раз в минуту (можно убрать, если не нужно).
     */
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $projectsCount      = Project::count();
        $bannersCount       = Banner::count();
        $cardsCount         = Card::count();
        $quotesCount        = Quote::count();
        $slidersCount       = Slider::count();
        $blogPostsCount     = BlogCard::count();
        $contentBlocksCount = ContentBlock::count();

        // Заполненность по языкам (просто число блоков на каждом языке)
        $locales = ['en', 'ru', 'ro'];

        $localeContent = collect($locales)->mapWithKeys(function (string $locale) {
            return [
                $locale => ContentBlock::where('locale', $locale)->count(),
            ];
        })->all();

        $localeDescription = sprintf(
            'EN: %d • RU: %d • RO: %d',
            $localeContent['en'] ?? 0,
            $localeContent['ru'] ?? 0,
            $localeContent['ro'] ?? 0,
        );

        // Процент проектов с картинкой — просто для красоты
        $projectsWithImage = Project::whereNotNull('image_path')->count();
        $projectsImagePercent = $projectsCount > 0
            ? round($projectsWithImage / max($projectsCount, 1) * 100)
            : 0;

        return [

            // 1. Проекты
            Stat::make('Projects', $projectsCount)
                ->description('Всего проектов на сайте')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('success')
                ->chart([3, 4, 5, 6, 7, 8, 9]),

            // 2. Баннеры (hero)
            Stat::make('Hero banners', $bannersCount)
                ->description('Слайдов в большом баннере')
                ->descriptionIcon('heroicon-o-photo')
                ->color('info')
                ->chart([1, 2, 3, 3, 4, 4, 5]),

            // 3. Карточки услуг
            Stat::make('Service cards', $cardsCount)
                ->description('Карточек в блоке «What we do»')
                ->descriptionIcon('heroicon-o-squares-2x2')
                ->color('warning')
                ->chart([2, 3, 4, 3, 4, 5, 6]),

            // 4. Отзывы / цитаты
            Stat::make('Testimonials', $quotesCount)
                ->description('Отзывы и цитаты на главной')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->chart([1, 1, 2, 2, 3, 3, 4]),

            // 5. Слайды в правом слайдере About
            Stat::make('About slider', $slidersCount)
                ->description('Кадров в слайдере «About»')
                ->descriptionIcon('heroicon-o-play-circle')
                ->color('gray')
                ->chart([1, 2, 2, 3, 3, 3, 4]),

            // 6. Записи в блоке Blog & Updates
            Stat::make('Blog updates', $blogPostsCount)
                ->description('Карточек в блоке «Blog & Updates»')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('amber')
                ->chart([0, 1, 1, 2, 2, 3, 3]),

            // 7. Контентные блоки (header, footer, about и т.п.)
            Stat::make('Content blocks', $contentBlocksCount)
                ->description('Текстовых блоков (header, footer, секции)')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('pink')
                ->chart([2, 3, 5, 6, 7, 9, 10]),

            // 8. Языковая заполненность
            Stat::make('Locales content', $localeDescription)
                ->description('Сколько блоков текста на каждом языке')
                ->descriptionIcon('heroicon-o-language')
                ->color('indigo')
                ->chart([1, 2, 3, 4, 5, 6, 7]),
        ];
    }
}
