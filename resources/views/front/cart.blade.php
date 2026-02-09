@extends('front.layouts.app')

@section('title', 'Корзина')

@section('content')

@php
    // корзина
    $cart = $cart ?? session('cart', []);
    $itemsCount = $itemsCount ?? collect($cart)->sum('qty');

    // купон
    $couponPercent = (int) (session('coupon.percent') ?? 0);
    $couponCode    = (string) (session('coupon.code') ?? '');

    // суммы
    $subTotalBase  = 0;
    $subTotalFinal = 0;
    $discount      = 0;

    foreach ($cart as $row) {
        $qty = (int)($row['qty'] ?? 0);

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

        $subTotalBase  += round($baseUnit  * $qty, 2);
        $subTotalFinal += round($finalUnit * $qty, 2);
    }

    $subTotalBase  = round($subTotalBase, 2);
    $subTotalFinal = round($subTotalFinal, 2);

    if ($couponPercent > 0) {
        $discount = max(0, round($subTotalBase - $subTotalFinal, 2));
    }
@endphp

{{-- HERO --}}
<section class="parallax-thight"
    style="background:url('{{ asset('tiband/img/banners/5.jpg') }}') no-repeat fixed center / cover;">
    <div class="container">
        <div class="text-left-1">
            <h1>ВАША КОРЗИНА</h1>
            <h4>
                У Вас добавлено
                <span data-cart-count>{{ $itemsCount }}</span> товара
            </h4>
        </div>
    </div>
</section>

{{-- CONTENT --}}
<section class="section section-margin">
    <div class="container">
        <div class="row">

            {{-- LEFT: ITEMS --}}
            <div class="col-md-7">

                {{-- ❗️ВАЖНО: эта форма НЕ отправляется --}}
                <form id="cartForm" action="javascript:void(0)" method="GET">
                    <ul class="cart-table">

                        <li class="cart-header">
                            <div class="col-lg-6">Название</div>
                            <div class="col-lg-2">Цена</div>
                            <div class="col-lg-2">Кол-во</div>
                            <div class="col-lg-2">ИТОГО</div>
                        </li>

                        @forelse($cart as $row)
                            @php
                                $pid = (string)($row['product_id'] ?? '');
                                $qty = (int)($row['qty'] ?? 0);
                                $img = $row['image'] ?: asset('tiband/img/products/1.jpg');

                                $baseUnit = isset($row['base_price'])
                                    ? (float)$row['base_price']
                                    : (float)($row['unit_price'] ?? 0);

                                if ($couponPercent > 0) {
                                    $finalUnit = isset($row['base_price'])
                                        ? (float)$row['unit_price']
                                        : round($baseUnit * (100 - $couponPercent) / 100, 2);
                                } else {
                                    $finalUnit = $baseUnit;
                                }

                                $lineBase  = round($baseUnit  * $qty, 2);
                                $lineFinal = round($finalUnit * $qty, 2);
                            @endphp

                            <li class="item" data-id="{{ $pid }}">

                                <div class="col-lg-1">
                                    <a href="#"
                                       class="js-cart-remove-page"
                                       data-id="{{ $pid }}">×</a>
                                </div>

                                <div class="col-lg-5">
                                    <div class="cart-img">
                                        <img src="{{ $img }}" alt="">
                                    </div>
                                    <h5>{{ $row['title'] }}</h5>
                                </div>

                                <div class="col-lg-2">
                                    @if($couponPercent > 0)
                                        <del>{{ number_format($baseUnit, 2, '.', ' ') }}</del>
                                    @endif
                                    {{ number_format($finalUnit, 2, '.', ' ') }}
                                </div>

                                <div class="col-lg-2">
                                    <div class="ammount js-qty-form">
                                        <button type="button" class="js-qty-minus">−</button>
                                        <input type="text" class="js-qty-input" value="{{ $qty }}">
                                        <button type="button" class="js-qty-plus">+</button>
                                    </div>
                                </div>

                                <div class="col-lg-2 js-line-total">
                                    @if($couponPercent > 0)
                                        <del>{{ number_format($lineBase, 2, '.', ' ') }}</del>
                                    @endif
                                    {{ number_format($lineFinal, 2, '.', ' ') }}
                                </div>

                            </li>
                        @empty
                            <li class="item" style="padding:20px; opacity:.7;">
                                Корзина пуста
                            </li>
                        @endforelse

                    </ul>
                </form>
            </div>

            {{-- RIGHT: TOTAL --}}
            <div class="col-md-5">

                <h5>Оформление заказа</h5>

                <ul class="pricing-table table-2-col">
                    <li>
                        <span>Сумма:</span>
                        <span id="sumTotal">{{ number_format($subTotalBase, 2, '.', ' ') }}</span>
                    </li>

                    @if($couponPercent > 0 && $itemsCount > 0)
                        <li>
                            <span>Купон:</span>
                            <span><strong>{{ $couponCode }}</strong> (-{{ $couponPercent }}%)</span>
                        </li>
                        <li>
                            <span>Скидка:</span>
                            <span>-{{ number_format($discount, 2, '.', ' ') }}</span>
                        </li>
                    @endif

                    <li>
                        <span>Доставка:</span>
                        <span>Бесплатно</span>
                    </li>

                    <li class="total">
                        <span>ИТОГО</span>
                        <span id="sumGrand">{{ number_format($subTotalFinal, 2, '.', ' ') }}</span>
                    </li>
                </ul>

                {{-- ✅ КУПОН --}}
                <form id="coupon"
                      method="POST"
                      action="{{ route('coupon.apply') }}">
                    @csrf

                    <input type="text"
                           name="code"
                           placeholder="Скидочный купон"
                           style="color:#000">

                    <button type="submit"
                            class="button-3 button-round button-small">
                        Проверить купон
                    </button>
                </form>

                <form action="{{ route('checkout') }}" method="POST" style="display:inline;">
    @csrf
    <button type="submit" class="button-3 button-round button-small push-right">
        К ОПЛАТЕ
    </button>
</form>


            </div>

        </div>
    </div>
</section>

@endsection
