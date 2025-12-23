@extends('front.layouts.app')

@section('title', 'Главная')

@section('content')
         <!-- ========= REVSLIDER ========== -->
            @if(($sectionActive['home-hero'] ?? false) && $banners->count())
<section id="revslider" class="fullwidthbanner-container">
    <div class="fullwidthbanner">
        <ul>

            @foreach($banners as $banner)
                <li data-transition="slidehorizontal" data-bgpositionend="center bottom">

                    <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title ?? '' }}"/>

                    {{-- ✅ ОПИСАНИЕ СВЕРХУ (как в верстке) --}}
                    @if(!empty($banner->text))
                        <div
                            class="tp-caption h5 normal-weight caption lfl whitefont"
                            data-easing="easeOutBack"
                            data-speed="1000"
                            data-start="500"
                            data-y="center"
                            data-x="left"
                            data-hoffset="100"
                            data-voffset="-68">
                            {{ $banner->text }}
                        </div>
                    @endif

                    {{-- ✅ ЗАГОЛОВОК --}}
                    @if(!empty($banner->title))
                        <div
                            class="tp-caption text-uppercase h0 normal-weight caption lfl whitefont"
                            data-easing="easeOutBack"
                            data-speed="1000"
                            data-start="500"
                            data-y="center"
                            data-x="left"
                            data-hoffset="100"
                            data-voffset="-26">
                            {{ $banner->title }}
                        </div>
                    @endif

                    {{-- ❗ СТАТИЧЕСКИЙ СЛОЙ --}}
                    <div
                        class="tp-caption h5 normal-weight caption lfl whitefont"
                        data-easing="easeOutBack"
                        data-speed="1000"
                        data-start="500"
                        data-y="center"
                        data-x="left"
                        data-hoffset="100"
                        data-voffset="68">
                        адаптивный дизайн
                    </div>

                </li>
            @endforeach

        </ul>
        <div class="tp-bannertimer"></div>
    </div>
</section>
@endif

            <!-- ========= END ========= -->
            
			<!-- ========= FULL WIDTH BOXES ========= -->
@if(($sectionActive['home-cards'] ?? false) && $homeCards->count())
<section class="section">
    <div class="container-fluid">
        <div class="marketing-box">

            @foreach($homeCards as $card)
                <div class="product">

                    {{-- КАРТИНКА --}}
                    @if(!empty($card->image_path))
                        <img
                            alt="{{ $card->title }}"
                            src="{{ asset('storage/'.$card->image_path) }}">
                    @endif

                    {{-- HOVER --}}
                    <div class="hover-mark">
                        <h2>{{ $card->title }}</h2>

                        @if(!empty($card->description))
                            <p>{{ $card->description }}</p>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>
    </div>
</section>
@endif


            <!-- ========= END ========= -->
            
            <!-- ========= TITLE ======== -->
            <section class="section-bar-white">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title-section">
                                <h1>СПЕЦИАЛЬНЫЕ ПРЕДЛОЖЕНИЯ</h1>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- ========= END======== -->
            
            <!-- ========= CAROUSEL  FEATURES PRODUCTS ========= -->
           @if(($sectionActive['spec-list'] ?? false) && $specList->count())
<section class="promo-section">
    <div class="container">

        <div class="promo-slider">

            @foreach($specList as $promo)
                <div class="promo-slide">

                    <div class="promo-content">
                        <h3 class="promo-title">
                            {{ $promo->title }}
                        </h3>

                        @if($promo->description)
                            <p class="promo-text">{{ $promo->description }}</p>
                        @endif

                        @if($promo->description_2)
                            <p class="promo-text muted">{{ $promo->description_2 }}</p>
                        @endif

                        @if($promo->link)
                            <a href="{{ $promo->link }}" class="promo-btn">
                                Узнать детали →
                            </a>
                        @endif
                    </div>

                    @if($promo->image_path)
                        <div class="promo-image">
                            <img src="{{ asset('storage/'.$promo->image_path) }}" alt="">
                        </div>
                    @endif

                </div>
            @endforeach

        </div>

    </div>
</section>
@endif
<style>
    /* SECTION */
.promo-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #111c31ff, #373169ff, #4f5da5ff);
    color: #fff;
    overflow: hidden;
}

/* SLIDER */
.promo-slider {
    display: flex;
    gap: 60px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    padding-bottom: 20px;
}

.promo-slider::-webkit-scrollbar {
    height: 6px;
}
.promo-slider::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
}

/* SLIDE */
.promo-slide {
    min-width: 100%;
    display: flex;
    align-items: center;
    gap: 60px;
    scroll-snap-align: start;
    animation: fadeSlide 0.8s ease forwards;
}

/* CONTENT */
.promo-content {
    max-width: 520px;
}

.promo-title {
    font-size: 38px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.promo-text {
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 12px;
}

.promo-text.muted {
    opacity: 0.8;
}

/* BUTTON */
.promo-btn {
    display: inline-block;
    margin-top: 25px;
    padding: 14px 34px;
    border-radius: 40px;
    background: #363863;
    background: linear-gradient(135deg, #7072a2ff, #d4d5deff);
    color: #ffffffff;
    font-weight: 600;
    text-decoration: none;
    transition: all .35s ease;
}

.promo-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0,0,0,.35);
}

/* IMAGE */
.promo-image img {
    max-width: 420px;
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(0,0,0,.4);
    transform: translateY(0);
    transition: transform .6s ease;
}

.promo-slide:hover .promo-image img {
    transform: translateY(-10px);
}

/* ANIMATION */
@keyframes fadeSlide {
    from {
        opacity: 0;
        transform: translateX(40px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* ADAPTIVE */
@media (max-width: 992px) {
    .promo-slide {
        flex-direction: column;
        text-align: center;
    }

    .promo-image img {
        max-width: 90%;
    }
}

    </style>
            <!-- ========= END ========= -->
            
            <!-- ========= TITLE ======== -->
            <section class="section-bar-white">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title-section">
                                <h1>Лучшие предложения</h1>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- ========= END======== -->
            
            <!-- ========= SHOP ========= -->
           @if(($sectionActive['catalog'] ?? false) && isset($catalogProducts) && $catalogProducts->count())
<section class="section">
    <div class="container">
        <div class="lowerpad row">

            @foreach($catalogProducts as $item)
                @php
                    // announce image
                    $announceImage = $item->announce_image_path
                        ? \Illuminate\Support\Facades\Storage::url($item->announce_image_path)
                        : null;

                    // modal id (unique)
                    $modalId = 'productModal-' . $item->id;

                    // базовая логика цены (как у тебя)
                    $hasSale = !empty($item->sale_price) && (float)$item->sale_price > 0;

                    // "базовая цена к оплате" без купона: если есть скидка sale_price — берём её, иначе price
                    $base = $hasSale ? (float)$item->sale_price : (float)$item->price;

                    // "старая цена" (для зачёркивания) — если есть sale_price
                    $old = $hasSale ? (float)$item->price : null;

                    // купон
                    $couponPercent = (int) (session('coupon.percent') ?? 0);
                    $couponCode = (string) (session('coupon.code') ?? '');

                    $final = $couponPercent > 0 ? round($base * (100 - $couponPercent) / 100, 2) : $base;

                    // images for modal (detail images)
                    $detailImages = $item->relationLoaded('images') ? $item->images : ($item->images ?? collect());

                    $gallery = ($detailImages && $detailImages->count())
                        ? $detailImages
                        : collect();
                @endphp

                {{-- CARD --}}
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="isotope-info">
                        @if($announceImage)
                            <img class="img-responsive" src="{{ $announceImage }}" alt=""/>
                        @else
                            <div style="width:100%;height:240px;background:#222;border-radius:6px;"></div>
                        @endif

                        <div class="hover-info">
                            <a data-toggle="modal" data-target="#{{ $modalId }}"
                               class="qv-button button-1 button-round button-small">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="#"
                               class="fg-button button-3 button-round button-small js-add-to-cart"
                               data-id="{{ $item->id }}">
                                В КОРЗИНУ
                            </a>
                        </div>
                    </div>

                    <div class="shop-item-label">
                        <h5>{{ $item->announce_title ?: $item->title }}</h5>

                        <div class="pull-left">
                            {{-- если есть sale_price — показываем старую цену --}}
                            @if($old)
                                <del class="reduction">{{ number_format($old, 2, '.', ' ') }}</del>
                            @endif

                            {{-- если есть купон — показываем base зачёркнутой и final рядом --}}
                            @if($couponPercent > 0)
                                <del class="reduction" style="margin-left:6px;">
                                    {{ number_format($base, 2, '.', ' ') }}
                                </del>
                                <span style="margin-left:6px;">
                                    {{ number_format($final, 2, '.', ' ') }}
                                </span>
                                <span style="display:inline-block;margin-left:6px;font-size:10px;
                                    padding:2px 8px;border-radius:999px;background:rgba(0,0,0,.06);">
                                    -{{ $couponPercent }}%
                                </span>
                            @else
                                <span>{{ number_format($base, 2, '.', ' ') }}</span>
                            @endif
                        </div>

                        <div class="rating pull-right">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>

                {{-- MODAL --}}
                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label">
                    <div class="modal-dialog modal-lg" id="quickview-modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row lowerpad">

                                            {{-- LEFT: GALLERY --}}
                                            <div class="col-lg-5 col-sm-5 col-xs-12">
                                                <div class="sp-wrap">

                                                    @if($gallery->count())
                                                        @foreach($gallery as $img)
                                                            @php $imgUrl = \Illuminate\Support\Facades\Storage::url($img->image_path); @endphp
                                                            <a href="{{ $imgUrl }}">
                                                                <img src="{{ $imgUrl }}" alt="">
                                                            </a>
                                                        @endforeach
                                                    @elseif($announceImage)
                                                        <a href="{{ $announceImage }}">
                                                            <img src="{{ $announceImage }}" alt="">
                                                        </a>
                                                    @else
                                                        <div style="width:100%;height:320px;background:#222;border-radius:6px;"></div>
                                                    @endif

                                                </div>
                                            </div>

                                            {{-- RIGHT: INFO --}}
                                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                                <div class="shop-item-label big">

                                                    {{-- Детальный заголовок --}}
                                                    <h5>{{ $item->title }}</h5>

                                                    {{-- Цена в модалке --}}
                                                    <span>
                                                        @if($old)
                                                            <del class="reduction">{{ number_format($old, 2, '.', ' ') }}</del>
                                                        @endif

                                                        @if($couponPercent > 0)
                                                            <del class="reduction" style="margin-left:6px;">
                                                                {{ number_format($base, 2, '.', ' ') }}
                                                            </del>
                                                            <strong style="margin-left:8px;">
                                                                {{ number_format($final, 2, '.', ' ') }}
                                                            </strong>
                                                            <span style="display:inline-block;margin-left:8px;font-size:11px;
                                                                padding:2px 10px;border-radius:999px;background:rgba(0,0,0,.06);">
                                                                купон -{{ $couponPercent }}%
                                                            </span>
                                                        @else
                                                            <strong style="margin-left:6px;">
                                                                {{ number_format($base, 2, '.', ' ') }}
                                                            </strong>
                                                        @endif
                                                    </span>

                                                    <div class="rating">
                                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>
                                                        <p>(понравилось 3 людям)</p>
                                                    </div>

                                                    {{-- Описание (как ты хотела: обычное + детальное) --}}
                                                    <div>
                                                        @if(!empty($item->announce_description))
                                                            <p style="opacity:.85;">{{ $item->announce_description }}</p>
                                                        @endif

                                                        @if(!empty($item->description))
                                                            <p>{{ $item->description }}</p>
                                                        @endif

                                                        @if(!empty($item->description_extra))
                                                            <div style="margin-top:10px;">
                                                                <p>{{ $item->description_extra }}</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Кол-во + В корзину (НЕ ТРОГАЮ твою логику) --}}
                                                    <div>
                                                        <form class="ammount js-qty-form">
                                                            <button type="button" class="js-qty-minus">-</button>

                                                            <input id="qty-{{ $item->id }}" type="text" class="js-qty-input" value="1"/>

                                                            <button type="button" class="js-qty-plus">+</button>

                                                            <button
                                                                type="button"
                                                                class="button-2 button-xsmall js-add-to-cart"
                                                                data-id="{{ $item->id }}"
                                                                data-qty-input="#qty-{{ $item->id }}"
                                                            >
                                                                В КОРЗИНУ
                                                            </button>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                            {{-- /RIGHT --}}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- /modal-body --}}
                        </div>
                    </div>
                </div>
                {{-- /MODAL --}}

            @endforeach

        </div>
    </div>
</section>
@endif



            <!-- ======= END ========= -->
			
			<!-- ========= PARALLAX ======== -->
           @if(($sectionActive['counter'] ?? false) && isset($counterCards) && $counterCards->count())
    @php
        // Если у Card нет поля под иконку — сделаем маппинг по позиции
        $icons = [
            1 => 'icon-basket',
            2 => 'icon-alarmclock',
            3 => 'icon-heart',
            4 => 'icon-mobile',
        ];
    @endphp

    <section class="parallax"
             style="background: transparent url('{{ asset('tiband/img/banners/3.jpg') }}') no-repeat fixed 50% 50px / cover ;">
        <div class="container">
            <div class="row">

                @foreach($counterCards->take(4) as $card)
                    @php
                        $icon = $icons[$card->position] ?? 'icon-basket';
                        // В твоём примере число лежит в description
                        $number = trim((string)($card->description ?? '0'));
                        $label  = trim((string)($card->title ?? ''));
                    @endphp

                    <div class="col-lg-3 col-sm-3 col-xs-12">
                        <div class="counter-3 number-container my-animation animated" data-perc="{{ $number }}">
                            <i class="counter-icon {{ $icon }}"></i>
                            <div class="number">{{ $number }}</div>
                            <h6>{{ $label }}</h6>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
@endif

            <!-- ========= END ========= -->
			
			<!-- ========= TITLE ======== -->
            <section class="section-bar-white">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title-section">
                                <h1>НАШИ НОВОСТИ</h1>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- ========= END======== -->
 <!-- ========= SLIDER ========= -->
@if(($sectionActive['news'] ?? false) && isset($news) && $news->count())
<section class="section">
    <div class="container-fluid">
        <div class="nopad owl-carousel portfolio-carousel">

            @foreach($news as $item)
                @php
                    // если есть внешняя ссылка — используем её, иначе ведём на страницу новости
                    $href = !empty($item->link)
                        ? $item->link
                        : route('news.show', $item->id);
                @endphp

                <a class="clearhover" href="{{ $href }}">
                    <div class="isotope-info">

                        {{-- картинка --}}
                        @if(!empty($item->image_path))
                            <img class="img-responsive" src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title ?? '' }}">
                        @else
                            <div style="height:260px;background:#222;"></div>
                        @endif

                        <div class="hover-info">
                            <div class="infobox-3">

                                {{-- День --}}
                                @if(!empty($item->description))
                                    <h4>{{ $item->description }}</h4>
                                @endif

                                <hr/>

                                {{-- Месяц --}}
                                @if(!empty($item->description_2))
                                    <h5>{{ $item->description_2 }}</h5>
                                @endif

                                {{-- Заголовок --}}
                                @if(!empty($item->title))
                                    <h2>{{ $item->title }}</h2>
                                @endif

                                {{-- subtitle --}}
                                @if(!empty($item->subtitle))
                                    <p style="margin-top:10px;">{{ $item->subtitle }}</p>
                                @endif

                            </div>
                        </div>

                    </div>
                </a>
            @endforeach

        </div>
    </div>
</section>
@endif


            <!-- ========= END ========= -->

            <!-- ========= TITLE ======== -->
            <section class="section-bar-white">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title-section">
                                <h1>ОТЗЫВЫ О КОМПАНИИ</h1>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========= TESTIMONIALS ======== -->
            @if(($sectionActive['reviews'] ?? false) && isset($reviews) && $reviews->count())
<section class="section section-margin" style="padding-top:0px !important;" show-overflow>
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="owl-carousel testimonials">

                    @foreach($reviews as $item)
                        <div class="infobox full-width center">

                            {{-- Заголовок --}}
                            @if(!empty($item->title))
                                <h3>{{ $item->title }}</h3>
                            @endif

                            {{-- Описание --}}
                            @if(!empty($item->description))
                                <span>{{ $item->description }}</span>
                            @endif

                            {{-- Автор (subtitle) + должность/доп. (description_2) --}}
                            @php
                                $authorLine = trim(collect([$item->subtitle, $item->description_2])->filter()->join(', '));
                            @endphp

                            @if(!empty($authorLine))
                                @if(!empty($item->link))
                                    <a href="{{ $item->link }}"><h6>{{ $authorLine }}</h6></a>
                                @else
                                    <h6>{{ $authorLine }}</h6>
                                @endif
                            @endif

                        </div>
                    @endforeach

                </div>

            </div>
        </div>
    </div>
</section>
@endif

            <!-- ========= END ========= -->
            
            <!-- subscribe-section 
            ================================================== -->
            <section id="subscribe-section">
                <div class="container">
                    <div class="subscribe-box">
                        <h2>НЕ ПРОПУСТИТЕ СКИДКИ И АКЦИИ! ПОДПИШИТЕСЬ!</h2>
                        <form class="subscribe-form">
                            <input type="text" name="subscribe" id="subscribe" placeholder="ВАША ПОЧТА"/>
                            <a class="button-1 button-small" href="#">ОФОРМИТЬ</a>
                        </form>
                    </div>
                </div>
            </section>
            <!-- End subscribe section -->
@endsection
       
            