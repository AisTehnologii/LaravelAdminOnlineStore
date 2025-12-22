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
                        <li><a href="#">Рубли <i class="fa fa-angle-down"></i></a>
                            <ul class="sub-menu">
                                <li><a href="#">Евро</a></li>
                                <li><a href="#">Доллары</a></li>
                                <li><a href="#">Фунты</a></li>
                            </ul>
                        </li>

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

                        <li><a href="#" data-toggle="modal" data-target="#LoginModal">Аккаунт</a></li>
                        <li><a data-toggle="modal" data-target="#SubscribePopup" href="#">ОКНО СКИДКИ</a></li>
                        <li><a data-toggle="modal" data-target="#CookiePopup" href="#">ОКНО ЗАКАЗА</a></li>
                    </ul>
                </div>

                {{-- Login Modal (оставляем здесь, чтобы был доступен из хедера) --}}
                <div>
                    <div id="LoginModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Ваш личный кабинет</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="table-1" class="select-table table-h table-1">
                                        <nav>
                                            <ul>
                                                <li class="active"><a href="#table-1-1">ВХОД</a></li>
                                                <li><a href="#table-1-2">РЕГИСТРАЦИЯ</a></li>
                                                <li><a href="#table-1-3">ПОДПИСКА</a></li>
                                            </ul>
                                        </nav>
                                        <ul>
                                            <li id="table-1-1" class="active">
                                                <div class="row">
                                                    <form class="form" method="post">
                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><input type="text" name="name" placeholder="ВАШ ЛОГИН"/></div>
                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><input type="text" name="password" placeholder="ПАРОЛЬ"/></div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><p class="fieldset"><input id="log-check" type="checkbox"><label>Запомнить меня</label></p></div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="submit" value="ГОТОВО"/></div>
                                                    </form>
                                                </div>
                                            </li>
                                            <li id="table-1-2">
                                                <div class="row">
                                                    <form class="form" method="post">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="email" name="email" placeholder="ВАШ EMAIL"/></div>
                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><input type="text" name="password" placeholder="ПАРОЛЬ"/></div>
                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><input type="text" name="re-password" placeholder="ЕЩЕ РАЗ ПАРОЛЬ"/></div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><p class="fieldset"><input id="log-check" type="checkbox"><label>Принимаю условия</label></p></div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="submit" value="ГОТОВО"/></div>
                                                    </form>
                                                </div>
                                            </li>
                                            <li id="table-1-3">
                                                <div class="row">
                                                    <form class="form" method="post">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" name="name" placeholder="ВАШ EMAIL"/></div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><p class="fieldset"><input id="log-check" type="checkbox"><label>Согласен получить письма</label></p></div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="submit" value="ГОТОВО"/></div>
                                                    </form>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <input name="s" placeholder="ИСКАТЬ НА САЙТЕ..." type="text" autofocus/>
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
                                                <li><a href="#">СПИСОК ТОВАРОВ</a></li>
                                                <li><a href="#">ОПИСАНИЕ ТОВАРА</a></li>
                                                <li><a href="#">КОРЗИНА</a></li>
                                                <li><a href="#">ОПЛАТА ТОВАРА</a></li>
                                            </ul>
                                        </li>

                                        <li>
                                            <a class="general" href="#">НОВОСТИ <i class="fa fa-angle-down"></i></a>
                                            <ul class="sub-menu">
                                                <li><a href="#">СПИСОК НОВОСТЕЙ</a></li>
                                                <li><a href="#">ОПИСАНИЕ НОВОСТИ</a></li>
                                            </ul>
                                        </li>

                                        <li><a class="general" href="#">КОНТАКТЫ</a></li>

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
                        <li><a href="#">МАГАЗИН</a></li>
                        <li><a href="#">НОВОСТИ</a></li>
                        <li><a href="#">КОНТАКТЫ</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </section>
</header>
<!-- ========= END HEADER ========= -->
