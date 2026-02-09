{{-- resources/views/front/partials/header.blade.php --}}

<!-- Top Bar ============================================= -->
<div id="top-bar">
    <div class="container clearfix">
        <div class="row">
            <div class="col-sm-6 hidden-xs">
                <p><strong>Тел.:</strong> +7 123 456 78 90 | <strong>Email:</strong> mail@mail.com</p>
            </div>

            <div class="col-sm-6 col_last pull-right nobottommargin">
                <div class="top-links">
                    <ul>
                        {{-- Locale --}}
                        <li>
                            <a href="#">
                                {{ strtoupper(app()->getLocale()) }}
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('set-locale', 'ru') }}">🇷🇺 RU</a></li>
                                <li><a href="{{ route('set-locale', 'ro') }}">🇷🇴 RO</a></li>
                                <li><a href="{{ route('set-locale', 'en') }}">🇬🇧 EN</a></li>
                            </ul>
                        </li>

                        {{-- Auth / Guest --}}
                        @auth
                            <li>
                                <a href="#" style="cursor:default;">
                                    {{ auth()->user()->name }}
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                            style="background:none;border:none;padding:0;margin:0;cursor:pointer;color:inherit;">
                                        ВЫЙТИ
                                    </button>
                                </form>
                            </li>
                        @else
                            <li><a href="#" data-toggle="modal" data-target="#LoginModal">Аккаунт</a></li>
                        @endauth

                        <li><a data-toggle="modal" data-target="#SubscribePopup" href="#">ОКНО СКИДКИ</a></li>
                        <li><a data-toggle="modal" data-target="#CookiePopup" href="#">ОКНО ЗАКАЗА</a></li>
                    </ul>
                </div>

                {{-- Login/Register Modal (только для гостей) --}}
                @guest
                    <div id="LoginModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Ваш личный кабинет</h4>
                                </div>

                                <div class="modal-body">

                                    {{-- Ошибки валидации (если логин/регистрация вернули ошибки) --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger" style="margin-bottom:15px;">
                                            <ul style="margin:0; padding-left:18px;">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div id="table-1" class="select-table table-h table-1">
                                        <nav>
                                            <ul>
                                                <li class="active"><a href="#table-1-1">ВХОД</a></li>
                                                <li><a href="#table-1-2">РЕГИСТРАЦИЯ</a></li>
                                                <li><a href="#table-1-3">ПОДПИСКА</a></li>
                                            </ul>
                                        </nav>

                                        <ul>
                                            {{-- LOGIN --}}
                                            <li id="table-1-1" class="active">
                                                <div class="row">
                                                    <form class="form" method="POST" action="{{ route('login') }}">
                                                        @csrf

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="email" name="email" placeholder="ВАШ EMAIL" required />
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="password" name="password" placeholder="ПАРОЛЬ" required />
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <p class="fieldset">
                                                                <input id="remember_me" name="remember" type="checkbox" value="1">
                                                                <label for="remember_me">Запомнить меня</label>
                                                            </p>
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="submit" value="ВОЙТИ" />
                                                        </div>
                                                    </form>
                                                </div>
                                            </li>

                                            {{-- REGISTER --}}
                                            <li id="table-1-2">
                                                <div class="row">
                                                    <form class="form" method="POST" action="{{ route('register') }}">
                                                        @csrf

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="text" name="name" placeholder="ВАШЕ ИМЯ" required />
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="email" name="email" placeholder="ВАШ EMAIL" required />
                                                        </div>

                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                            <input type="password" name="password" placeholder="ПАРОЛЬ" required />
                                                        </div>

                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                            <input type="password" name="password_confirmation" placeholder="ЕЩЕ РАЗ ПАРОЛЬ" required />
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <p class="fieldset">
                                                                <input id="terms" name="terms" type="checkbox" value="1" required>
                                                                <label for="terms">Принимаю условия</label>
                                                            </p>
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="submit" value="СОЗДАТЬ АККАУНТ" />
                                                        </div>
                                                    </form>
                                                </div>
                                            </li>

                                            {{-- SUBSCRIBE (без POST на / !!!) --}}
                                            <li id="table-1-3">
                                                <div class="row">
                                                    <form class="form" onsubmit="return false;">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="email" name="email" placeholder="ВАШ EMAIL" required />
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <p class="fieldset">
                                                                <input id="sub_check" name="sub_check" type="checkbox" value="1" required>
                                                                <label for="sub_check">Согласен получить письма</label>
                                                            </p>
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <input type="submit" value="ГОТОВО" />
                                                        </div>
                                                    </form>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endguest
                {{-- /Login Modal --}}

            </div>
        </div>
    </div>
</div>
<!-- #top-bar end -->


<!-- ========= HEADER ========= -->
<header class="absolute-header">
    <section class="sticky-wrapper">
        <div id="main-menu-2" class="main-menu">
            <div class="container">
                <div class="row">
                    <nav class="col-md-12">

                        <div id="search-container">
                            <div class="menu-center">
                                <form action="{{ route('search.index') }}" method="GET" id="siteSearchForm" style="width:100%;">
                                    <input id="siteSearchInput"
                                           class="site-search-input"
                                           name="q"
                                           placeholder="ИСКАТЬ НА САЙТЕ..."
                                           type="text"
                                           autocomplete="off" />
                                </form>

                                <div id="siteSearchDropdown" class="site-search-dropdown" style="display:none;"></div>
                                <div class="xbutton main-menu-search">X</div>
                            </div>
                        </div>

                        <div id="menu-main">
                            <div class="navbar-left">
                                <div class="menu-center">
                                    <ul>
                                        <li>
                                            <a class="nopad" href="{{ url('/') }}">
                                                <img src="{{ asset('tiband/img/logo%402x.png') }}" alt=""/>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="navbar-right hidden-lg">
                                <div class="menu-center">
                                    <ul class="nav-pills nav menu-lowerpad">
                                        <li><a class="general mobile-menu-toggle"><div class="clearfix bars-icon "><hr/><hr/><hr/></div></a></li>
                                        <li><a class="toggle-1-button"><i class="fa fa-shopping-cart"></i></a></li>
                                        <li><a class="general main-menu-search"><i class="fa fa-search"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="navbar-right visible-lg">
                                <div class="menu-center">
                                    <ul class="nav-pills navbar-right nav">
                                        <li><a class="general {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">ГЛАВНАЯ</a></li>

                                        <li>
                                            <a class="general" href="#">МАГАЗИН <i class="fa fa-angle-down"></i></a>
                                            <ul class="sub-menu">
                                                <li><a href="/products">СПИСОК ТОВАРОВ</a></li>
                                                <li><a href="/cart">КОРЗИНА</a></li>
                                                <li><a href="#">ОПЛАТА ТОВАРА</a></li>
                                            </ul>
                                        </li>

                                        <li><a class="general" href="/news">НОВОСТИ</a></li>
                                        <li><a class="general" href="/contacts">КОНТАКТЫ</a></li>
        @auth
    <li>
        <a href="{{ route('orders.index') }}">МОИ ЗАКАЗЫ</a>
    </li>
@endauth
                                        <li>
                                            @php
                                                $cart = session('cart', []);
                                                $cartCount = array_sum(array_column($cart, 'qty'));
                                            @endphp

                                            <a class="clearhover toggle-1-button">
                                                <div class="clearfix cart">
                                                    <i class="fa fa-shopping-cart"></i>
                                                    <output>КОРЗИНА (<span data-cart-count>{{ $cartCount }}</span>)</output>
                                                </div>
                                            </a>
                                        </li>

                                        <li><a class="general nopad main-menu-search"><i class="fa fa-search"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </nav>
                </div>
            </div>

            <div class="mobile-menu">
                <div class="container">
                    <ul>
                        <li><a href="{{ url('/') }}">ГЛАВНАЯ</a></li>
                        <li><a href="/products">МАГАЗИН</a></li>
                        <li><a href="/news">НОВОСТИ</a></li>
                        <li><a href="/contacts">КОНТАКТЫ</a></li>
                        @auth
    <li>
        <a href="{{ route('orders.index') }}">МОИ ЗАКАЗЫ</a>
    </li>
@endauth
                    </ul>
                </div>
            </div>

        </div>
    </section>
</header>
<!-- ========= END HEADER ========= -->


{{-- Если были ошибки — при загрузке сразу открываем модалку (чтобы ты их увидела) --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.modal) {
                jQuery('#LoginModal').modal('show');
            }
        });
    </script>
@endif
