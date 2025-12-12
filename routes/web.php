<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Banner;
use App\Models\Slider;
use App\Models\ContentBlock;
use App\Models\Card;
use App\Models\Project;
use App\Models\Quote;
use App\Models\BlogCard;

// список поддерживаемых языков
$availableLocales = ['en', 'ru', 'ro'];

Route::get('/set-locale/{locale}', function (string $locale) use ($availableLocales) {
    if (! in_array($locale, $availableLocales, true)) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    // возвращаем обратно на ту же страницу
    return back();
})->name('set-locale');


Route::get('/', function () {
    $locale = app()->getLocale();

    // Блоки с данными из обычных таблиц
    $banners   = Banner::where('locale', $locale)->orderBy('position')->get();
    $sliders   = Slider::where('locale', $locale)->orderBy('position')->get();
    $cards     = Card::where('locale', $locale)->orderBy('position')->get();
    $projects  = Project::where('locale', $locale)->orderBy('position')->get();
    $quotes    = Quote::where('locale', $locale)->orderBy('position')->get();
    $blogCards = BlogCard::where('locale', $locale)->orderBy('position')->get();

    // ABOUT CARD (как было)
    $aboutCard = ContentBlock::where('section', 'home.about_card')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    // BLOG INTRO (как было)
    $blogIntro = ContentBlock::where('section', 'home.blog_intro')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    // НОВОЕ: header (меню, логотип, кнопка hero)
    $layoutHeader = ContentBlock::where('section', 'layout.header')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    // НОВОЕ: заголовки секций на главной (What We Do, Our Projects, View All Projects)
    $homeSections = ContentBlock::where('section', 'home.sections')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    // НОВОЕ: footer (About, Features, Subscribe, Follow, копирайт)
    $footerBlocks = ContentBlock::where('section', 'layout.footer')
        ->where('locale', $locale)
        ->pluck('value', 'key');

    return view('front.home', compact(
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


// Тест админа (как было)
Route::get('/admin-test', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    if (!Gate::allows('is-admin')) {
        abort(403);
    }
    return 'Admin test OK';
});
