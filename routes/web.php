<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Models\ContentSection;
use App\Models\ContentBlock;

use App\Models\Banner;
use App\Models\Slider;
use App\Models\Card;
use App\Models\Project;
use App\Models\Quote;
use App\Models\BlogCard;

use App\Http\Controllers\ChatAttachmentController;

use App\Http\Controllers\BackupDownloadController;

// список поддерживаемых языков
$availableLocales = ['en', 'ru', 'ro'];

Route::get('/set-locale/{locale}', function (string $locale) use ($availableLocales) {
    if (! in_array($locale, $availableLocales, true)) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    return back();
})->name('set-locale');


/**
 * HOME (/)
 * Важно: сущности берём строго по section_id, а секции рисуем только если is_active=1
 */
Route::get('/', function () {
    $locale = app()->getLocale();

    // 1) SECTION IDs (slug -> id, только активные)
    $sections = [
        'home-hero'          => ContentSection::query()->where('slug', 'home-hero')->where('is_active', true)->value('id'),
        'home-about-slider'  => ContentSection::query()->where('slug', 'home-about-slider')->where('is_active', true)->value('id'),
        'home-services'      => ContentSection::query()->where('slug', 'home-services')->where('is_active', true)->value('id'),
        'home-projects'      => ContentSection::query()->where('slug', 'home-projects')->where('is_active', true)->value('id'),
        'home-testimonials'  => ContentSection::query()->where('slug', 'home-testimonials')->where('is_active', true)->value('id'),
        'home-blog'          => ContentSection::query()->where('slug', 'home-blog')->where('is_active', true)->value('id'),

        // если хочешь управлять футером через ContentSections — создай такую секцию и включи/выключай
        // иначе футер будет показываться всегда (см. ниже фолбек)
        'layout-footer'      => ContentSection::query()->where('slug', 'layout-footer')->where('is_active', true)->value('id'),
    ];

    // 2) Флаги активности (если id есть -> секция активна)
    $sectionActive = [
        'home-hero'         => !empty($sections['home-hero']),
        'home-about-slider' => !empty($sections['home-about-slider']),
        'home-services'     => !empty($sections['home-services']),
        'home-projects'     => !empty($sections['home-projects']),
        'home-testimonials' => !empty($sections['home-testimonials']),
        'home-blog'         => !empty($sections['home-blog']),

        // Если не хочешь добавлять ContentSection для футера — сделай всегда true:
        // 'layout-footer' => true
        'layout-footer'     => !empty($sections['layout-footer']) ? true : true, // ✅ по умолчанию футер показываем
    ];

    // 3) ENTITIES (строго по section_id)
    $banners = Banner::query()
        ->where('locale', $locale)
        ->when($sections['home-hero'], fn ($q) => $q->where('section_id', $sections['home-hero']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $sliders = Slider::query()
        ->where('locale', $locale)
        ->when($sections['home-about-slider'], fn ($q) => $q->where('section_id', $sections['home-about-slider']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $cards = Card::query()
        ->where('locale', $locale)
        ->when($sections['home-services'], fn ($q) => $q->where('section_id', $sections['home-services']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $projects = Project::query()
        ->where('locale', $locale)
        ->when($sections['home-projects'], fn ($q) => $q->where('section_id', $sections['home-projects']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $quotes = Quote::query()
        ->where('locale', $locale)
        ->when($sections['home-testimonials'], fn ($q) => $q->where('section_id', $sections['home-testimonials']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $blogCards = BlogCard::query()
        ->where('locale', $locale)
        ->when($sections['home-blog'], fn ($q) => $q->where('section_id', $sections['home-blog']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    // 4) CONTENT BLOCKS (как в верстке)
    $aboutCard = ContentBlock::where('section', 'home.about_card')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    $blogIntro = ContentBlock::where('section', 'home.blog_intro')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    $layoutHeader = ContentBlock::where('section', 'layout.header')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    $homeSections = ContentBlock::where('section', 'home.sections')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    $footerBlocks = ContentBlock::where('section', 'layout.footer')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    return view('front.home', compact(
        'sectionActive',
        'banners',
        'sliders',
        'cards',
        'projects',
        'quotes',
        'blogCards',
        'aboutCard',
        'blogIntro',
        'layoutHeader',
        'homeSections',
        'footerBlocks',
    ));
});


/**
 * Пример второй страницы: ABOUT (/about)
 * Тут баннеры только из секции about-hero (если активна)
 */
Route::get('/about', function () {
    $locale = app()->getLocale();

    $aboutHeroSectionId = ContentSection::query()
        ->where('slug', 'about-hero')
        ->where('is_active', true)
        ->value('id');

    $sectionActive = [
        'about-hero' => !empty($aboutHeroSectionId),
    ];

    $banners = Banner::query()
        ->where('locale', $locale)
        ->when($aboutHeroSectionId, fn ($q) => $q->where('section_id', $aboutHeroSectionId), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    return view('front.about', compact('sectionActive', 'banners'));
});


Route::middleware(['web', 'auth'])
    ->get('/chat/attachments/{attachment}', [ChatAttachmentController::class, 'download'])
    ->name('chat.attachments.download');

    Route::post('/admin/ping', function () {
    if (!Auth::check()) {
        return response()->json(['ok' => false], 401);
    }

    Auth::user()->forceFill(['last_seen_at' => now()])->saveQuietly();

    return response()->json(['ok' => true]);
})->middleware(['auth']) // <- web НЕ нужно, он уже есть в web.php
  ->name('admin.ping');


  Route::middleware(['web', 'auth'])
    ->get('/admin/backups/{backup}/download', BackupDownloadController::class)
    ->name('backups.download');
/**
 * Тест админа (как было)
 */
Route::get('/admin-test', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    if (!Gate::allows('is-admin')) {
        abort(403);
    }
    return 'Admin test OK';
});


