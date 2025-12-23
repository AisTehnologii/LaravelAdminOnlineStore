@extends('front.layouts.app')

@section('title', 'Корзина')

@section('content')

@php
    // корзина
    $cart = $cart ?? session('cart', []);
    $itemsCount = $itemsCount ?? collect($cart)->sum('qty');

    // купон из сессии
    $couponPercent = (int) (session('coupon.percent') ?? 0);
    $couponCode = (string) (session('coupon.code') ?? '');

    // суммы
    $subTotalBase  = 0; // "как было" (без купона)
    $subTotalFinal = 0; // "как стало" (с купоном)
    $discount = 0;

    foreach ($cart as $row) {
        $qty = (int)($row['qty'] ?? 0);

        // base_price появляется после /coupon/apply (у тебя так и сделано в web.php)
        $baseUnit = isset($row['base_price'])
            ? (float)$row['base_price']
            : (float)($row['unit_price'] ?? 0);

        // финальная цена:
        // - если base_price есть => unit_price уже со скидкой
        // - если base_price нет => считаем скидку здесь
        if ($couponPercent > 0) {
            $finalUnit = isset($row['base_price'])
                ? (float)($row['unit_price'] ?? 0)
                : round($baseUnit * (100 - $couponPercent) / 100, 2);
        } else {
            $finalUnit = $baseUnit;
        }

        $subTotalBase  += round($baseUnit * $qty, 2);
        $subTotalFinal += round($finalUnit * $qty, 2);
    }

    $subTotalBase  = round($subTotalBase, 2);
    $subTotalFinal = round($subTotalFinal, 2);

    if ($couponPercent > 0) {
        $discount = max(0, round($subTotalBase - $subTotalFinal, 2));
    }
@endphp

<section class="parallax-thight" style="background: transparent url('{{ asset('tiband/img/banners/5.jpg') }}') no-repeat fixed 50% 50px / cover ;">
    <div class="container">
        <div class="row">
            <div class="text-left-1">
                <h1>ВАША КОРЗИНА</h1>
                <h4>У Вас добавлено <span data-cart-count>{{ $itemsCount }}</span> товара</h4>
            </div>
        </div>
    </div>
</section>

<section class="section section-margin">
    <div class="container">
        <div class="row">
            <div class="col-md-7">

                <form id="cartForm">
                    <ul class="cart-table">
                        <li>
                            <div class="col-lg-6 col-sm-6 col-xs-12">Название</div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">Цена</div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">Кол-во</div>
                            <div class="col-lg-2 col-sm-2 col-xs-12">ИТОГО</div>
                        </li>

                        @forelse($cart as $row)
                            @php
                                $img = $row['image'] ?: asset('tiband/img/products/1.jpg');
                                $qty = (int) ($row['qty'] ?? 0);

                                $baseUnit = isset($row['base_price'])
                                    ? (float)$row['base_price']
                                    : (float)($row['unit_price'] ?? 0);

                                if ($couponPercent > 0) {
                                    $finalUnit = isset($row['base_price'])
                                        ? (float)($row['unit_price'] ?? 0)
                                        : round($baseUnit * (100 - $couponPercent) / 100, 2);
                                } else {
                                    $finalUnit = $baseUnit;
                                }

                                $lineBase  = round($baseUnit * $qty, 2);
                                $lineFinal = round($finalUnit * $qty, 2);

                                // id в data-id берем тот же, что в remove/update в твоём JS
                                $pid = (string)($row['product_id'] ?? '');
                            @endphp

                            <li class="item" data-id="{{ $pid }}">
                                <div class="col-lg-1 col-sm-1 col-xs-2">
                                    <a href="#" class="js-cart-remove-page" data-id="{{ $pid }}">X</a>
                                </div>

                                <div class="col-lg-5 col-sm-5 col-xs-10">
                                    <div class="cart-img"><img src="{{ $img }}"></div>
                                    <div class="shop-item-label">
                                        <h5>{{ $row['title'] }}</h5>
                                        <div class="rating">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-o"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-sm-2 col-xs-12">
                                    @if($couponPercent > 0)
                                        <del style="opacity:.65; margin-right:6px;">
                                            {{ number_format($baseUnit, 2, '.', ' ') }}
                                        </del>
                                        {{ number_format($finalUnit, 2, '.', ' ') }}
                                    @else
                                        {{ number_format($finalUnit, 2, '.', ' ') }}
                                    @endif
                                </div>

                                <div class="col-lg-2 col-sm-2 col-xs-12">
                                    <div class="ammount js-qty-form">
                                        <button class="js-qty-minus" type="button">-</button>
                                        <input type="text" class="js-qty-input" value="{{ $qty }}"/>
                                        <button class="js-qty-plus" type="button">+</button>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-sm-2 col-xs-12 js-line-total">
                                    @if($couponPercent > 0)
                                        <del style="opacity:.65; margin-right:6px;">
                                            {{ number_format($lineBase, 2, '.', ' ') }}
                                        </del>
                                        {{ number_format($lineFinal, 2, '.', ' ') }}
                                    @else
                                        {{ number_format($lineFinal, 2, '.', ' ') }}
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="item" style="padding:20px; opacity:.7;">Корзина пуста</li>
                        @endforelse
                    </ul>
                </form>

            </div>

            <div class="col-md-5">
                <div class="row">
                    <div class="col-lg-12 col-sm-12 col-xs-12">
                        <div class="shop-item-label">
                            <h5>Оформление заказа</h5>
                        </div>

                        <ul class="pricing-table table-2-col">
                            <li class="top-border nopad">
                                <span>Сумма:</span>
                                <span id="sumTotal">{{ number_format($subTotalBase, 2, '.', ' ') }}</span>
                            </li>

                            @if($couponPercent > 0 && $itemsCount > 0)
                                <li class="nopad">
                                    <span>Купон:</span>
                                    <span><strong>{{ $couponCode ?: '—' }}</strong> (-{{ $couponPercent }}%)</span>
                                </li>
                                <li class="nopad">
                                    <span>Скидка:</span>
                                    <span>-{{ number_format($discount, 2, '.', ' ') }}</span>
                                </li>
                            @endif

                            <li class="nopad"><span>Доставка:</span><span>Бесплатно</span></li>

                            <li class="nopad">
                                <span>ИТОГО</span>
                                <span id="sumGrand">{{ number_format($subTotalFinal, 2, '.', ' ') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <form id="coupon">
                            <input type="text" placeholder="Скидочный купон" name="code" style="color:#000 !important;"/>
                            <input type="submit" value="Проверить купон" class="button-3 button-round button-small">
                        </form>

                        <a href="#" class="button-3 button-round button-small push-right">К ОПЛАТЕ</a>
                        <a href="#" id="recalcBtn" class="button-3 button-round button-small push-right">ПЕРЕСЧИТАТЬ</a>

                        @if($couponPercent > 0 && $itemsCount > 0)
                            <div style="margin-top:10px; opacity:.85;">
                                Купон активен: <strong>{{ $couponCode ?: '—' }}</strong>, скидка {{ $couponPercent }}%
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
