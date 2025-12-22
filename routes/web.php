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
use App\Models\PromoBlock;
use App\Models\Product;

use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\BackupDownloadController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


/**
 * Поддерживаемые языки
 */
$availableLocales = ['en', 'ru', 'ro'];

/**
 * Переключение языка
 */
Route::get('/set-locale/{locale}', function (string $locale) use ($availableLocales) {
    if (!in_array($locale, $availableLocales, true)) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    return back();
})->name('set-locale');


/**
 * HOME (/)
 */
Route::get('/', function () {
    $locale = app()->getLocale();

    /**
     * 1) SECTION IDs (slug -> id, только активные)
     */
    $sections = [
        // Основные секции
        'home-hero'         => ContentSection::query()->where('slug', 'home-hero')->where('is_active', true)->value('id'),
        'home-cards'        => ContentSection::query()->where('slug', 'home-cards')->where('is_active', true)->value('id'),
        'home-about-slider' => ContentSection::query()->where('slug', 'home-about-slider')->where('is_active', true)->value('id'),
        'home-services'     => ContentSection::query()->where('slug', 'home-services')->where('is_active', true)->value('id'),
        'home-projects'     => ContentSection::query()->where('slug', 'home-projects')->where('is_active', true)->value('id'),
        'home-testimonials' => ContentSection::query()->where('slug', 'home-testimonials')->where('is_active', true)->value('id'),
        'home-blog'         => ContentSection::query()->where('slug', 'home-blog')->where('is_active', true)->value('id'),

        // Promo blocks
        'spec-list'         => ContentSection::query()->where('slug', 'spec-list')->where('is_active', true)->value('id'),
        'reviews'           => ContentSection::query()->where('slug', 'reviews')->where('is_active', true)->value('id'),
        'news'              => ContentSection::query()->where('slug', 'news')->where('is_active', true)->value('id'),

        // Cards
        'counter'           => ContentSection::query()->where('slug', 'counter')->where('is_active', true)->value('id'),

        // ✅ Catalog (Products)
        'catalog'           => ContentSection::query()->where('slug', 'catalog')->where('is_active', true)->value('id'),

        // Footer
        'layout-footer'     => ContentSection::query()->where('slug', 'layout-footer')->where('is_active', true)->value('id'),
    ];

    /**
     * 2) Флаги активности секций
     */
    $sectionActive = [
        'home-hero'         => !empty($sections['home-hero']),
        'home-cards'        => !empty($sections['home-cards']),
        'home-about-slider' => !empty($sections['home-about-slider']),
        'home-services'     => !empty($sections['home-services']),
        'home-projects'     => !empty($sections['home-projects']),
        'home-testimonials' => !empty($sections['home-testimonials']),
        'home-blog'         => !empty($sections['home-blog']),

        'spec-list'         => !empty($sections['spec-list']),
        'reviews'           => !empty($sections['reviews']),
        'news'              => !empty($sections['news']),

        'counter'           => !empty($sections['counter']),

        // ✅ catalog
        'catalog'           => !empty($sections['catalog']),

        // футер показываем всегда
        'layout-footer'     => true,
    ];

    /**
     * 3) ENTITIES (строго по section_id + locale)
     */
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

    $homeCards = Card::query()
        ->where('locale', $locale)
        ->when($sections['home-cards'], fn ($q) => $q->where('section_id', $sections['home-cards']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $serviceCards = Card::query()
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

    // Promo blocks
    $specList = PromoBlock::query()
        ->where('locale', $locale)
        ->when($sections['spec-list'], fn ($q) => $q->where('section_id', $sections['spec-list']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $reviews = PromoBlock::query()
        ->where('locale', $locale)
        ->when($sections['reviews'], fn ($q) => $q->where('section_id', $sections['reviews']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    $news = PromoBlock::query()
        ->where('locale', $locale)
        ->when($sections['news'], fn ($q) => $q->where('section_id', $sections['news']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    // Cards: counter
    $counterCards = Card::query()
        ->where('locale', $locale)
        ->when($sections['counter'], fn ($q) => $q->where('section_id', $sections['counter']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    // ✅ Products: catalog
    $catalogProducts = Product::query()
        ->where('locale', $locale)
        ->where('is_active', true)
        ->with(['images' => fn ($q) => $q->orderBy('position')])
        ->when($sections['catalog'], fn ($q) => $q->where('section_id', $sections['catalog']), fn ($q) => $q->whereRaw('1=0'))
        ->orderBy('position')
        ->get();

    /**
     * 4) CONTENT BLOCKS
     */
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
        'homeCards',
        'serviceCards',
        'projects',
        'quotes',
        'blogCards',
        'specList',
        'reviews',
        'news',
        'counterCards',
        'catalogProducts',
        'aboutCard',
        'blogIntro',
        'layoutHeader',
        'homeSections',
        'footerBlocks',
    ));
});


/**
 * ABOUT (/about)
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


/**
 * Скачивание вложений чата
 */
Route::middleware(['web', 'auth'])
    ->get('/chat/attachments/{attachment}', [ChatAttachmentController::class, 'download'])
    ->name('chat.attachments.download');


/**
 * Ping активности в админке
 */
Route::post('/admin/ping', function () {
    if (!Auth::check()) {
        return response()->json(['ok' => false], 401);
    }

    Auth::user()->forceFill(['last_seen_at' => now()])->saveQuietly();

    return response()->json(['ok' => true]);
})->middleware(['auth'])
  ->name('admin.ping');


/**
 * Скачивание backup
 */
Route::middleware(['web', 'auth'])
    ->get('/admin/backups/{backup}/download', BackupDownloadController::class)
    ->name('backups.download');
// =======================
// CART (session-based)
// =======================

function cart_recalc(array $cart): array
{
    $count = 0;
    $sum   = 0.0;

    foreach ($cart as $row) {
        $q = (int)($row['qty'] ?? 0);
        $p = (float)($row['unit_price'] ?? 0);
        $count += $q;
        $sum   += $p * $q;
    }

    return [$count, $sum];
}

Route::get('/cart', function () {
    $cart = session('cart', []);

    [$itemsCount, $subTotal] = cart_recalc($cart);

    return view('front.cart', compact('cart', 'itemsCount', 'subTotal'));
})->name('cart.index');


// ADD: принимает qty (из модалки/карточки)
Route::post('/cart/add/{product}', function (Product $product, Request $request) {
    $qty = max(1, (int) $request->input('qty', 1));

    // картинки для превью
    $product->loadMissing(['images' => fn ($q) => $q->orderBy('position')]);

    $cart = session('cart', []);
    $id   = (string) $product->id;

    $unitPrice = (!empty($product->sale_price) && (float)$product->sale_price > 0)
        ? (float)$product->sale_price
        : (float)$product->price;

    $imageUrl = null;
    if (!empty($product->announce_image_path)) {
        $imageUrl = Storage::url($product->announce_image_path);
    } elseif ($product->relationLoaded('images') && $product->images->count()) {
        $imageUrl = Storage::url($product->images->first()->image_path);
    }

    if (!isset($cart[$id])) {
        $cart[$id] = [
            'product_id' => (int) $product->id,
            'title'      => $product->announce_title ?: $product->title,
            'qty'        => 0,
            'unit_price' => $unitPrice,
            'image'      => $imageUrl,
        ];
    }

    $cart[$id]['qty'] = (int)($cart[$id]['qty'] ?? 0) + $qty;

    session(['cart' => $cart]);

    [$count, $sum] = cart_recalc($cart);

    return response()->json([
        'ok'    => true,
        'count' => $count,
        'sum'   => $sum,
        'cart'  => $cart,
    ]);
})->name('cart.add');


// UPDATE: установить qty (для +/- на странице cart)
Route::post('/cart/update/{id}', function (Request $request, string $id) {
    $cart = session('cart', []);

    if (!isset($cart[$id])) {
        return response()->json(['ok' => false, 'message' => 'Not found'], 404);
    }

    $qty = max(1, (int) $request->input('qty', 1));
    $cart[$id]['qty'] = $qty;

    session(['cart' => $cart]);

    [$count, $sum] = cart_recalc($cart);

    $lineTotal = (float)($cart[$id]['unit_price'] ?? 0) * (int)($cart[$id]['qty'] ?? 0);

    return response()->json([
        'ok'        => true,
        'id'        => $id,
        'count'     => $count,
        'sum'       => $sum,
        'lineTotal' => $lineTotal,
        'cart'      => $cart,
    ]);
})->name('cart.update');


// REMOVE: удаление по id (без Product $product — быстрее и без путаницы)
Route::post('/cart/remove/{id}', function (string $id) {
    $cart = session('cart', []);

    unset($cart[$id]);

    session(['cart' => $cart]);

    [$count, $sum] = cart_recalc($cart);

    return response()->json([
        'ok'    => true,
        'count' => $count,
        'sum'   => $sum,
        'cart'  => $cart,
    ]);
})->name('cart.remove');


// CLEAR (опционально, но полезно)
Route::post('/cart/clear', function () {
    session()->forget('cart');

    return response()->json([
        'ok'    => true,
        'count' => 0,
        'sum'   => 0,
        'cart'  => [],
    ]);
})->name('cart.clear');


// STATE: текущее состояние корзины (для initial render в JS если нужно)
Route::get('/cart/state', function () {
    $cart = session('cart', []);
    [$count, $sum] = cart_recalc($cart);

    return response()->json([
        'ok'    => true,
        'count' => $count,
        'sum'   => $sum,
        'cart'  => $cart,
    ]);
})->name('cart.state');

/**
 * Тест админа
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
