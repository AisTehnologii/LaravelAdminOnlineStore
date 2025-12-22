@extends('front.layouts.app')

@section('title', 'Главная')

@section('content')

            @php
  // $cart, $itemsCount, $subTotal приходят из web.php -> route('cart.index')
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
                                $lineTotal = ((float)$row['unit_price']) * ((int)$row['qty']);
                            @endphp

                            <li class="item" data-id="{{ $row['product_id'] }}">
                                <div class="col-lg-1 col-sm-1 col-xs-2">
                                    <a href="#" class="js-cart-remove-page" data-id="{{ $row['product_id'] }}">X</a>
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
                                    {{ number_format($row['unit_price'], 2, '.', ' ') }}
                                </div>

                                <div class="col-lg-2 col-sm-2 col-xs-12">
                                    <div class="ammount js-qty-form">
  <button class="js-qty-minus" type="button">-</button>
  <input type="text" class="js-qty-input" value="{{ (int)$row['qty'] }}"/>
  <button class="js-qty-plus" type="button">+</button>
</div>

                                </div>

                                <div class="col-lg-2 col-sm-2 col-xs-12 js-line-total">
                                    {{ number_format($lineTotal, 2, '.', ' ') }}
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
                            <li class="top-border nopad"><span>Сумма:</span><span id="sumTotal">{{ number_format($subTotal, 2, '.', ' ') }}</span></li>
                            <li class="nopad"><span>Доставка:</span><span>Бесплатно</span></li>
                            <li class="nopad"><span>ИТОГО</span><span id="sumGrand">{{ number_format($subTotal, 2, '.', ' ') }}</span></li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <form id="coupon">
                            <input type="text" placeholder="Скидочный купон" name="code"/>
                            <input type="submit" value="Проверить купон" class="button-3 button-round button-small">
                        </form>

                        <a href="#" class="button-3 button-round button-small push-right">К ОПЛАТЕ</a>
                        <a href="#" id="recalcBtn" class="button-3 button-round button-small push-right">ПЕРЕСЧИТАТЬ</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

            <!-- ========= END ========= -->
@endsection
       
            