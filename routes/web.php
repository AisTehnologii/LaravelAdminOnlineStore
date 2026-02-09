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

use App\Models\Coupon;
use App\Support\Pricing;

use App\Models\Order;
use App\Models\OrderItem;

use App\Http\Controllers\OneC\OneCExchangeController;

Route::match(['GET','POST'], '/1c/exchange', [OneCExchangeController::class, 'handle']);

// ✅ ловим /1c/exchange/getgoods, /1c/exchange/something и т.д.
Route::match(['GET','POST'], '/1c/exchange/{tail}', [OneCExchangeController::class, 'handle'])
    ->where('tail', '.*');


/**
 * Поддерживаемые языки
 */
$availableLocales = ['en', 'ru', 'ro'];

/**
 * Переключение языка
 */
Route::get('/set-locale/{locale}', function (string $locale) {
    $availableLocales = ['en', 'ru', 'ro'];

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
if (! function_exists('cart_recalc')) {
    function cart_recalc(array $cart, ?array $coupon = null): array
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



Route::get('/products', function (Request $request) {

    $locale   = app()->getLocale();
    $fallback = config('app.fallback_locale', 'ru');

    $catalogSectionId = ContentSection::query()
        ->where('slug', 'catalog')
        ->where('is_active', true)
        ->value('id');

    $cart = session('cart', []);
    $itemsCount = collect($cart)->sum('qty');

    if (!$catalogSectionId) {
        return view('front.products', [
            'products' => collect(),
            'popularProducts' => collect(),
            'itemsCount' => $itemsCount,
            'priceMin' => 0,
            'priceMax' => 0,
            'appliedMin' => null,
            'appliedMax' => null,
            'sort' => 'position',
            'dir' => 'asc',
        ]);
    }

    // 1) база по текущей локали
    $base = Product::query()
        ->where('is_active', true)
        ->where('section_id', $catalogSectionId)
        ->where('locale', $locale)
        ->with(['images' => fn($q) => $q->orderBy('position')]);

    // 2) если в текущей локали пусто — берём fallback (обычно ru)
    if (!$base->clone()->exists() && $fallback && $fallback !== $locale) {
        $base = Product::query()
            ->where('is_active', true)
            ->where('section_id', $catalogSectionId)
            ->where('locale', $fallback)
            ->with(['images' => fn($q) => $q->orderBy('position')]);
    }

    // min/max уже по правильной базе
    $minDb = (clone $base)->min(DB::raw('COALESCE(NULLIF(sale_price,0), price)')) ?? 0;
    $maxDb = (clone $base)->max(DB::raw('COALESCE(NULLIF(sale_price,0), price)')) ?? 0;

    $q = clone $base;

    $apply = $request->query('apply') === '1';
    $appliedMin = null;
    $appliedMax = null;

    if ($apply) {
        $min = $request->filled('min') ? (float)$request->query('min') : null;
        $max = $request->filled('max') ? (float)$request->query('max') : null;

        if ($min !== null) $min = max((float)$minDb, $min);
        if ($max !== null) $max = min((float)$maxDb, $max);

        if ($min !== null) {
            $q->whereRaw('COALESCE(NULLIF(sale_price,0), price) >= ?', [$min]);
            $appliedMin = $min;
        }
        if ($max !== null) {
            $q->whereRaw('COALESCE(NULLIF(sale_price,0), price) <= ?', [$max]);
            $appliedMax = $max;
        }
    }

    $sort = $request->query('sort', 'position');
    $dir  = $request->query('dir', 'asc');
    $dir  = in_array($dir, ['asc', 'desc']) ? $dir : 'asc';

    if ($sort === 'price') {
        $q->orderByRaw('COALESCE(NULLIF(sale_price,0), price) ' . $dir);
    } elseif ($sort === 'newest') {
        $q->orderBy('id', $dir);
    } else {
        $q->orderBy('position', $dir)->orderBy('id', 'desc');
    }

    $products = $q->paginate(12)->withQueryString();

    $popularProducts = (clone $base)
        ->orderBy('position', 'asc')
        ->orderBy('id', 'desc')
        ->take(6)
        ->get();

    return view('front.products', [
        'products' => $products,
        'popularProducts' => $popularProducts,
        'itemsCount' => $itemsCount,
        'priceMin' => (float)$minDb,
        'priceMax' => (float)$maxDb,
        'appliedMin' => $appliedMin,
        'appliedMax' => $appliedMax,
        'sort' => $sort,
        'dir' => $dir,
    ]);

})->name('products.index');



Route::get('/news', function (Request $request) {

    $locale = app()->getLocale();

    // корзина для счетчиков в хедере
    $cart = session('cart', []);
    $itemsCount = collect($cart)->sum('qty');

    /*
    |--------------------------------------------------------------------------
    | section_id для "news"
    |--------------------------------------------------------------------------
    */
    $newsSectionQ = ContentSection::query()->where('slug', 'news');

    if (Schema::hasColumn('content_sections', 'is_active')) {
        $newsSectionQ->where('is_active', 1);
    }

    $newsSectionId = $newsSectionQ->value('id');

    // если секция выключена/не найдена
    if (!$newsSectionId) {
        return view('front.news.index', [
            'news' => collect([]),
            'latestNews' => collect([]),
            'reviews' => collect([]),
            'itemsCount' => $itemsCount,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | NEWS: базовый запрос как на главной (promo_blocks секции news)
    |--------------------------------------------------------------------------
    */
    $q = PromoBlock::query();

    if (Schema::hasColumn('promo_blocks', 'locale')) {
        $q->where('locale', $locale);
    }

    if (Schema::hasColumn('promo_blocks', 'section_id')) {
        $q->where('section_id', $newsSectionId);
    }

    if (Schema::hasColumn('promo_blocks', 'is_active')) {
        $q->where('is_active', 1);
    }

    if (Schema::hasColumn('promo_blocks', 'position')) {
        $q->orderBy('position');
    }

    $q->orderByDesc('id');

    // список
    $news = (clone $q)->paginate(12)->withQueryString();

    // sidebar "последние"
    $latestNews = (clone $q)->limit(6)->get();

    /*
    |--------------------------------------------------------------------------
    | REVIEWS: для блока "ЛУЧШИЕ ОТЗЫВЫ" (promo_blocks секции reviews)
    |--------------------------------------------------------------------------
    */
    $reviewsSectionQ = ContentSection::query()->where('slug', 'reviews');

    if (Schema::hasColumn('content_sections', 'is_active')) {
        $reviewsSectionQ->where('is_active', 1);
    }

    $reviewsSectionId = $reviewsSectionQ->value('id');

    $reviews = collect([]);

    if ($reviewsSectionId) {
        $rq = PromoBlock::query();

        if (Schema::hasColumn('promo_blocks', 'locale')) {
            $rq->where('locale', $locale);
        }

        if (Schema::hasColumn('promo_blocks', 'section_id')) {
            $rq->where('section_id', $reviewsSectionId);
        }

        if (Schema::hasColumn('promo_blocks', 'is_active')) {
            $rq->where('is_active', 1);
        }

        if (Schema::hasColumn('promo_blocks', 'position')) {
            $rq->orderBy('position');
        }

        $rq->orderByDesc('id');

        // в сайдбар обычно 4 хватает
        $reviews = $rq->limit(4)->get();
    }

    return view('front.news.index', compact('news', 'latestNews', 'reviews', 'itemsCount'));

})->name('news.index');


Route::get('/news/{id}', function ($id) {
    $locale = app()->getLocale();

    $newsSectionId = ContentSection::query()
        ->where('slug', 'news')
        ->where('is_active', true)
        ->value('id');

    // корзина для счетчиков
    $cart = session('cart', []);
    $itemsCount = collect($cart)->sum('qty');

    // одна новость (только из нужной секции и locale)
    $postQ = PromoBlock::query()
        ->where('locale', $locale)
        ->when($newsSectionId, fn($q) => $q->where('section_id', $newsSectionId));

    if (Schema::hasColumn('promo_blocks', 'is_active')) {
        $postQ->where('is_active', 1);
    }

    $post = $postQ->findOrFail($id);

    // для сайдбара/похожих
    $q = PromoBlock::query()
        ->where('locale', $locale)
        ->when($newsSectionId, fn($qq) => $qq->where('section_id', $newsSectionId));

    if (Schema::hasColumn('promo_blocks', 'is_active')) {
        $q->where('is_active', 1);
    }

    if (Schema::hasColumn('promo_blocks', 'position')) {
        $q->orderBy('position');
    }
    $q->orderByDesc('id');

    $latestNews = (clone $q)->limit(6)->get();

    $relatedNews = (clone $q)
        ->where('id', '!=', $post->id)
        ->inRandomOrder()
        ->limit(3)
        ->get();

    return view('front.news.show', compact('post', 'latestNews', 'relatedNews', 'itemsCount'));
})->name('news.show');


Route::get('/contacts', function (Request $request) {

    // корзина для счётчиков в хедере
    $cart = session('cart', []);
    $itemsCount = collect($cart)->sum('qty');

    return view('front.contacts', compact('itemsCount'));
})->name('contacts.index');



Route::get('/search', function (Request $request) {
    $q = trim((string)$request->get('q', ''));
    $locale = app()->getLocale();

    // корзина для счетчика в хедере (чтобы не падало, если где-то используется)
    $cart = session('cart', []);
    $itemsCount = collect($cart)->sum('qty');

    if ($q === '') {
        return view('front.search.index', [
            'q' => $q,
            'products' => collect([]),
            'news' => collect([]),
            'itemsCount' => $itemsCount,
        ]);
    }

    // ----------------------------
    // PRODUCTS (товары)
    // ----------------------------
    $productsQuery = Product::query();

    // если есть locale/is_active — учитываем
    if (Schema::hasColumn('products', 'locale')) {
        $productsQuery->where('locale', $locale);
    }
    if (Schema::hasColumn('products', 'is_active')) {
        $productsQuery->where('is_active', 1);
    }

    // поиск по нескольким полям (что есть в проекте)
    $productsQuery->where(function ($w) use ($q) {
        $like = '%' . $q . '%';
        if (Schema::hasColumn('products', 'title')) {
            $w->orWhere('title', 'like', $like);
        }
        if (Schema::hasColumn('products', 'announce_title')) {
            $w->orWhere('announce_title', 'like', $like);
        }
        if (Schema::hasColumn('products', 'announce_description')) {
            $w->orWhere('announce_description', 'like', $like);
        }
        if (Schema::hasColumn('products', 'description')) {
            $w->orWhere('description', 'like', $like);
        }
    });

    if (Schema::hasColumn('products', 'position')) {
        $productsQuery->orderBy('position');
    }
    $productsQuery->orderByDesc('id');

    $products = $productsQuery->limit(20)->get();

    // ----------------------------
    // NEWS (новости) = promo_blocks секции "news"
    // ----------------------------
    $news = collect([]);

    $newsSectionId = ContentSection::query()
        ->where('slug', 'news')
        ->where('is_active', true)
        ->value('id');

    if ($newsSectionId) {
        $newsQuery = PromoBlock::query()
            ->where('section_id', $newsSectionId);

        if (Schema::hasColumn('promo_blocks', 'locale')) {
            $newsQuery->where('locale', $locale);
        }
        if (Schema::hasColumn('promo_blocks', 'is_active')) {
            $newsQuery->where('is_active', 1);
        }

        $newsQuery->where(function ($w) use ($q) {
            $like = '%' . $q . '%';
            if (Schema::hasColumn('promo_blocks', 'title')) {
                $w->orWhere('title', 'like', $like);
            }
            if (Schema::hasColumn('promo_blocks', 'subtitle')) {
                $w->orWhere('subtitle', 'like', $like);
            }
            if (Schema::hasColumn('promo_blocks', 'description')) {
                $w->orWhere('description', 'like', $like);
            }
            if (Schema::hasColumn('promo_blocks', 'description_2')) {
                $w->orWhere('description_2', 'like', $like);
            }
        });

        if (Schema::hasColumn('promo_blocks', 'position')) {
            $newsQuery->orderBy('position');
        }
        $newsQuery->orderByDesc('id');

        $news = $newsQuery->limit(20)->get();
    }

    return view('front.search.index', compact('q', 'products', 'news', 'itemsCount'));
})->name('search.index');


// JSON подсказки для header (autocomplete)
Route::get('/search/suggest', function (Request $request) {
    $q = trim((string)$request->get('q', ''));
    $locale = app()->getLocale();

    if ($q === '' || mb_strlen($q) < 2) {
        return response()->json([
            'ok' => true,
            'items' => [],
        ]);
    }

    $items = [];

    // товары
    $productsQuery = Product::query();

    if (Schema::hasColumn('products', 'locale')) {
        $productsQuery->where('locale', $locale);
    }
    if (Schema::hasColumn('products', 'is_active')) {
        $productsQuery->where('is_active', 1);
    }

    $productsQuery->where(function ($w) use ($q) {
        $like = '%' . $q . '%';
        if (Schema::hasColumn('products', 'title')) {
            $w->orWhere('title', 'like', $like);
        }
        if (Schema::hasColumn('products', 'announce_title')) {
            $w->orWhere('announce_title', 'like', $like);
        }
    });

    $products = $productsQuery->orderByDesc('id')->limit(6)->get();

    foreach ($products as $p) {
        $title = $p->title ?? $p->announce_title ?? ('Product #' . $p->id);

        // ведём на страницу товаров и передаём product=id (у тебя уже можно открыть модалку по параметру)
        $items[] = [
            'type' => 'product',
            'title' => $title,
            'url' => url('/products') . '?product=' . $p->id,
        ];
    }

    // новости
    $newsSectionId = ContentSection::query()
        ->where('slug', 'news')
        ->where('is_active', true)
        ->value('id');

    if ($newsSectionId) {
        $newsQuery = PromoBlock::query()
            ->where('section_id', $newsSectionId);

        if (Schema::hasColumn('promo_blocks', 'locale')) {
            $newsQuery->where('locale', $locale);
        }
        if (Schema::hasColumn('promo_blocks', 'is_active')) {
            $newsQuery->where('is_active', 1);
        }

        $newsQuery->where(function ($w) use ($q) {
            $like = '%' . $q . '%';
            if (Schema::hasColumn('promo_blocks', 'title')) {
                $w->orWhere('title', 'like', $like);
            }
            if (Schema::hasColumn('promo_blocks', 'subtitle')) {
                $w->orWhere('subtitle', 'like', $like);
            }
        });

        $news = $newsQuery->orderByDesc('id')->limit(6)->get();

        foreach ($news as $n) {
            $items[] = [
                'type' => 'news',
                'title' => $n->title ?? ('News #' . $n->id),
                'url' => route('news.show', $n->id),
            ];
        }
    }

    return response()->json([
        'ok' => true,
        'items' => $items,
    ]);
})->name('search.suggest');



Route::post('/coupon/apply', function (Request $request) {
    $code = Coupon::normalize((string)$request->input('code', ''));

    if ($code === '') {
        return response()->json(['ok' => false, 'message' => 'Введите купон.'], 422);
    }

    $coupon = Coupon::query()->active()->where('code', $code)->first();

    if (!$coupon) {
        return response()->json(['ok' => false, 'message' => 'Купон недействителен или отключён.'], 404);
    }

    // сохраняем купон в session
    session(['coupon' => ['code' => $coupon->code, 'percent' => (int)$coupon->percent]]);

    // пересчитываем корзину "на лету"
    $cart = session('cart', []);
    $percent = (int)$coupon->percent;

    foreach ($cart as $pid => $row) {
        $product = Product::query()->find($pid);
        if (!$product) continue;

        $base = Pricing::basePrice($product);
        $effective = Pricing::applyPercent($base, $percent);

        $cart[$pid]['base_price'] = $base;         // сохраняем для справки (не обязательно)
        $cart[$pid]['unit_price'] = $effective;    // ВАЖНО: тут цена со скидкой
    }

    session(['cart' => $cart]);

    $sum = collect($cart)->sum(fn($r) => (float)$r['unit_price'] * (int)$r['qty']);
    $count = collect($cart)->sum(fn($r) => (int)$r['qty']);

    return response()->json([
        'ok' => true,
        'message' => "Купон применён: -{$percent}%",
        'coupon' => session('coupon'),
        'cart' => $cart,
        'sum' => round($sum, 2),
        'count' => $count,
    ]);
})->name('coupon.apply');

Route::post('/coupon/clear', function () {
    session()->forget('coupon');

    // пересчитываем корзину обратно на базовые цены
    $cart = session('cart', []);

    foreach ($cart as $pid => $row) {
        $product = Product::query()->find($pid);
        if (!$product) continue;

        $base = Pricing::basePrice($product);
        $cart[$pid]['base_price'] = $base;
        $cart[$pid]['unit_price'] = $base; // без скидки
    }

    session(['cart' => $cart]);

    $sum = collect($cart)->sum(fn($r) => (float)$r['unit_price'] * (int)$r['qty']);
    $count = collect($cart)->sum(fn($r) => (int)$r['qty']);

    return response()->json([
        'ok' => true,
        'message' => "Купон удалён",
        'cart' => $cart,
        'sum' => round($sum, 2),
        'count' => $count,
    ]);
})->name('coupon.clear');

Route::post('/coupon/remove', function (Request $request) {
    $request->session()->forget('coupon'); // ['code'=>..., 'percent'=>...]
    // если нужно — можно вернуть пересчитанную корзину тоже
    $cart = session('cart', []);
    $count = collect($cart)->sum('qty');
    $sum = 0;
    foreach ($cart as $row) $sum += (float)$row['unit_price'] * (int)$row['qty'];

    return response()->json([
        'ok' => true,
        'coupon' => null,
        'cart' => $cart,
        'count' => $count,
        'sum' => $sum,
        'message' => 'Купон удалён',
    ]);
})->name('coupon.remove');


Route::get('/coupon/current', function (Request $request) {
    $coupon = $request->session()->get('coupon'); // ['code'=>..., 'percent'=>...]
    return response()->json([
        'ok' => true,
        'coupon' => $coupon ?: null,
    ]);
})->name('coupon.current');


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

require __DIR__.'/auth.php';



Route::post('/contact/send', function (Request $request) {
    // TODO: обработка (mail/telegram/db)
    return back()->with('success', 'Сообщение отправлено!');
})->name('contact.send');


Route::post('/checkout', function (Request $request) {

    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect('/cart')->with('error', 'Корзина пуста.');
    }

    [$itemsCount, $grandTotal] = cart_recalc($cart, session('coupon'));

   DB::transaction(function () use ($cart, $grandTotal) {

    // 1) Берём последний номер и блокируем строку/таблицу на время транзакции
    $last = DB::table('orders')
        ->select('number')
        ->where('number', 'like', 'AA%')
        ->orderByDesc('id')
        ->lockForUpdate()
        ->first();

    $nextInt = 1;

    if ($last && !empty($last->number)) {
        // AA00000001 -> 1
        $nextInt = (int) substr($last->number, 2) + 1;
    }

    $nextNumber = 'AA' . str_pad((string)$nextInt, 8, '0', STR_PAD_LEFT);

    // 2) Создаём заказ
    $order = Order::create([
         'user_id' => auth()->id(),
        'number'      => $nextNumber,
        'ordered_at'  => now(),
        'grand_total' => (float) $grandTotal,
        'currency'    => 'MDL',
        'status'      => 'new',
        'raw'         => json_encode(['cart' => $cart], JSON_UNESCAPED_UNICODE),
    ]);

    // 3) Пишем items (цены!)
   foreach ($cart as $row) {
    $qty  = (float) ($row['qty'] ?? 0);
    $unit = (float) ($row['unit_price'] ?? 0);

    $order->items()->create([
        'product_id'         => (int) ($row['product_id'] ?? 0),
        'product_title'      => (string) ($row['title'] ?? ''), // ✅ ВОТ ЭТО ВАЖНО
        'quantity'           => $qty,
        'unit_amount'        => $unit,
        'total_amount'       => round($unit * $qty, 2),
        'raw'                => $row,
    ]);
}
app(\App\Services\OneC\OrderCommerceMlExportService::class)->buildAndStore($order);

});




    // очищаем корзину (как ты хочешь)
    session()->forget('cart');
    // coupon можно оставить или убрать — чаще убирают:
    // session()->forget('coupon');

    return redirect('/cart')->with('success', 'Заказ создан!');

})->name('checkout');





Route::middleware('auth')->group(function () {
    Route::get('/my-orders', function () {
        $orders = \App\Models\Order::query()
            ->where('user_id', auth()->id())
            ->with(['items.product']) // ✅ подтягиваем Product для каждой позиции
            ->orderByDesc('ordered_at')
            ->paginate(10);

        return view('front.my-orders', compact('orders'));
    })->name('orders.index');
});
















