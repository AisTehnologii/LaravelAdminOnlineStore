<!-- =============== FOOTER ============ -->
<footer id="footer">
    <section class="footer footer-margin">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-3 col-xs-12">
                    <div class="widget widget-about-us la-hover">
                        <h5>О КОМПАНИИ</h5>
                        <p>Россия, Москва</p>
                        <p>ул. Пушкина 23а</p>
                        <p>E-Mail:  mail@mail.com</p>
                        <p>Phone: +7 123 456 78 90</p>
                        <ul class="nav-pills nav">
                            <li><img class="payment-card" src="{{ asset('tiband/img/payment-method-1.png') }}" alt=""/></li>
                            <li><img class="payment-card" src="{{ asset('tiband/img/payment-method-2.png') }}" alt=""/></li>
                            <li><img class="payment-card" src="{{ asset('tiband/img/payment-method-3.png') }}" alt=""/></li>
                            <li><img class="payment-card" src="{{ asset('tiband/img/payment-method-4.png') }}" alt=""/></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-3 col-xs-12">
                    <div class="widget la-hover">
                        <h5>Последние товары</h5>
                        <ul>
                            <li><h6><a href="#">Название товара</a></h6></li>
                            <li><h6><a href="#">Название товара</a></h6></li>
                            <li><h6><a href="#">Название товара</a></h6></li>
                            <li><h6><a href="#">Название товара</a></h6></li>
                            <li><h6><a href="#">Название товара</a></h6></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-3 col-xs-12">
                    <div class="widget la-hover">
                        <h5>Последние новости</h5>
                        <ul>
                            <li><h6><a href="#">Заголовок новости на сайте</a></h6></li>
                            <li><h6><a href="#">Заголовок новости на сайте</a></h6></li>
                            <li><h6><a href="#">Заголовок новости на сайте</a></h6></li>
                            <li><h6><a href="#">Заголовок новости на сайте</a></h6></li>
                            <li><h6><a href="#">Заголовок новости на сайте</a></h6></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-3 col-xs-12">
                    <div class="widget la-hover">
                        <h5>Фотогалерея</h5>
                        <div class="flickr-container" data-count="8" data-userid="72842304@N04" data-apikey="817d59b480172b8e698db8da5335a2d1"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="center">
                        <p class="footer">© 2016 Ti-Band - шаблон интернет-магазина и услуг</p>
                    </div>
                    <a href="#" class="footer-toggle"><i class="fa fa-angle-down"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section id="footer-toggle">
        <ul>
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li><a href="#"><i class="fa fa-tumblr"></i></a></li>
            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
            <li><a href="#"><i class="fa fa-youtube"></i></a></li>
            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
        </ul>
    </section>
</footer>

<a class="scroll-top" href="#"><i class="fa fa-angle-up"></i></a>

<!-- ========= SIDE MENU (CART) ======= -->
@php
  $cart = session('cart', []);
  $cartCount = array_sum(array_column($cart, 'qty'));
  $cartSum = 0;
  foreach ($cart as $row) {
      $cartSum += ((float)$row['unit_price']) * ((int)$row['qty']);
  }
@endphp

<section id="toggle-1" class="sidemenu">
    <div class="verticalblock">
        <div class="toggle-1-button xbutton">Закрыть</div>

       <h5>У Вас в корзине <span data-cart-count>{{ $cartCount }}</span> товаров</h5>

        <hr/>

        <ul class="cart-items" id="sideCartItems">
            @forelse($cart as $row)
                @php
                    $lineTotal = ((float)$row['unit_price']) * ((int)$row['qty']);
                    $img = $row['image'] ?: asset('tiband/img/products/1.jpg');
                @endphp

                <li data-product-id="{{ $row['product_id'] }}">
                    <a href="#" class="close-button js-cart-remove" data-id="{{ $row['product_id'] }}">X</a>

                    <a href="#">
                        {{ $row['title'] }}
                        <output>{{ $row['qty'] }} шт, {{ number_format($lineTotal, 2, '.', ' ') }}</output>
                    </a>

                    <img src="{{ $img }}" alt=""/>
                </li>
            @empty
                <li style="padding:10px 0; opacity:.7;">Корзина пуста</li>
            @endforelse
        </ul>

        <output id="sideCartTotal">ИТОГО: {{ number_format($cartSum, 2, '.', ' ') }}</output>
        <hr/>

        <div class="side-menu-button">
            <a href="{{ route('cart.index') }}">КОРЗИНА</a>
        </div>
        <div class="side-menu-button">
            <a href="{{ route('cart.index') }}">ОПЛАТИТЬ</a>
        </div>

        {{-- Похожие товары пока оставь как есть (потом подключим реально) --}}
        <section id="recent-projects" class="widget widget-recent-projects">
            <h5 id="viewed">ПОХОЖИЕ ТОВАРЫ</h5>
            <div class="imgbox">
                <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/products/10.jpg') }}" alt=""/></a></div>
                <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/products/11.jpg') }}" alt=""/></a></div>
                <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/products/12.jpg') }}" alt=""/></a></div>
                <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/products/13.jpg') }}" alt=""/></a></div>
            </div>
        </section>
    </div>
</section>


<!-- ========= POPUPS ========= -->
<div id="SubscribePopup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="background: transparent url('{{ asset('tiband/img/modal1.jpg') }}') repeat scroll 0% 0% / cover ;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">У ВАС ЕСТЬ СКИДОЧНЫЙ КУПОН?</h4>
            </div>
            <div class="modal-body">
                <p>ВВЕДИТЕ ЕГО ЗЕСЬ И СЕЙЧАС!</p>
                <form class="subscribe-form-popup">
                    <input name="subscribe-popup" class="subscribe-popup" placeholder="ВАШ ККУПОН" type="text">
                    <button class="button-1 button-medium" type="submit">ПРОВЕРИТЬ!</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="CookiePopup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ВЫ ДЕЙСТВИТЕЛЬНО ХОТИТЕ СДЕЛАТЬ ЗАКАЗ?</h4>
            </div>
            <div class="modal-body">
                <p>Согласно предыдущему, CTR оправдывает потребительский бренд...</p>
                <form class="subscribe-form-popup">
                    <button class="button-1 button-medium" type="submit">НЕТ, Я ПЕРЕДУМАЛ</button>
                    <button class="button-1 button-medium" type="submit">КОНЕЧНО, ДА!</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ======== SCRIPTS ======== -->
<script src="{{ asset('tiband/js/plugins.js') }}"></script>
<script src="{{ asset('tiband/rs-plugin/js/jquery.themepunch.tools.min.js') }}"></script>
<script src="{{ asset('tiband/rs-plugin/js/jquery.themepunch.revolution.min.js') }}"></script>
<script src="{{ asset('tiband/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('tiband/js/scripts.js') }}"></script>
<script>
(function () {
  if (window.__cartInited) return;
  window.__cartInited = true;

  function csrf() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  }

  async function postJson(url, data) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf(),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: JSON.stringify(data || {}),
    });

    if (!res.ok) {
      const text = await res.text().catch(() => '');
      console.error('Cart request failed:', res.status, text);
      return { ok: false, status: res.status, text };
    }

    return await res.json();
  }

  function setAllCartCounts(count) {
    document.querySelectorAll('[data-cart-count]').forEach(el => {
      el.textContent = String(count);
    });
  }

  function money(n) {
    const x = Number(n || 0);
    // формат без пробелов, т.к. обновляем динамически
    return x.toFixed(2);
  }

  function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, (m) => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;',
    }[m]));
  }

  function renderSideCart(cart, sum, count) {
    setAllCartCounts(count);

    const totalEl = document.getElementById('sideCartTotal');
    if (totalEl) totalEl.textContent = 'ИТОГО: ' + money(sum);

    const ul = document.getElementById('sideCartItems');
    if (!ul) return;

    const keys = Object.keys(cart || {});
    if (!keys.length) {
      ul.innerHTML = '<li style="padding:10px 0; opacity:.7;">Корзина пуста</li>';
      return;
    }

    const fallbackImg = "{{ asset('tiband/img/products/1.jpg') }}";

    ul.innerHTML = keys.map(k => {
      const row = cart[k] || {};
      const img = row.image ? row.image : fallbackImg;
      const lineTotal = money(Number(row.unit_price) * Number(row.qty));

      return `
        <li data-product-id="${row.product_id}">
          <a href="#" class="close-button js-cart-remove" data-id="${row.product_id}">X</a>
          <a href="#">
            ${escapeHtml(row.title)}
            <output>${Number(row.qty)} шт, ${lineTotal}</output>
          </a>
          <img src="${img}" alt=""/>
        </li>
      `;
    }).join('');
  }

  // ----------------------------
  // +/- (модалка + cart page)
  // ----------------------------
  document.addEventListener('click', async function (e) {
    const plus = e.target.closest('.js-qty-plus');
    const minus = e.target.closest('.js-qty-minus');
    const btnEl = plus || minus;
    if (!btnEl) return;

    e.preventDefault();

    // анти-двойное срабатывание, если обработчик задублирован
    if (btnEl.dataset.cartLock === '1') return;
    btnEl.dataset.cartLock = '1';
    setTimeout(() => { btnEl.dataset.cartLock = '0'; }, 120);

    // контейнер строго от кнопки
    const container = btnEl.closest('.js-qty-form') || btnEl.closest('.ammount');
    if (!container) return;

    const input = container.querySelector('.js-qty-input');
    if (!input) return;

    let v = parseInt(input.value || '1', 10);
    if (Number.isNaN(v) || v < 1) v = 1;

    if (plus) v += 1;
    if (minus) v = Math.max(1, v - 1);

    input.value = v;

    // Если это страница cart — обновляем сервер + суммы
    const row = btnEl.closest('li.item[data-id]');
    if (!row) return;

    const id = row.getAttribute('data-id');
    const data = await postJson(`/cart/update/${id}`, { qty: v });

    if (data && data.ok) {
      // line total в строке
      const line = row.querySelector('.js-line-total');
      if (line) line.textContent = money(data.lineTotal);

      // суммы справа
      const sumTotal = document.getElementById('sumTotal');
      const sumGrand = document.getElementById('sumGrand');
      if (sumTotal) sumTotal.textContent = money(data.sum);
      if (sumGrand) sumGrand.textContent = money(data.sum);

      // side-cart + счётчики
      renderSideCart(data.cart, data.sum, data.count);
    }
  });

  // ----------------------------
  // Add to cart (qty из модалки)
  // ----------------------------
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-add-to-cart');
    if (!btn) return;

    e.preventDefault();

    const id = btn.dataset.id;
    if (!id) return;

    let qty = 1;

    // 1) если указан data-qty-input="#qty-..."
    if (btn.dataset.qtyInput) {
      const el = document.querySelector(btn.dataset.qtyInput);
      const v = parseInt(el?.value || '1', 10);
      qty = (!Number.isNaN(v) && v > 0) ? v : 1;
    } else {
      // 2) иначе ищем рядом
      const container = btn.closest('.js-qty-form') || btn.closest('.ammount');
      const input = container ? container.querySelector('.js-qty-input') : null;
      const v = parseInt(input?.value || '1', 10);
      qty = (!Number.isNaN(v) && v > 0) ? v : 1;
    }

    const data = await postJson(`/cart/add/${id}`, { qty });

    if (data && data.ok) {
      renderSideCart(data.cart, data.sum, data.count);
    }
  });

  // ----------------------------
  // Remove (side-cart)
  // ----------------------------
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-cart-remove');
    if (!btn) return;

    e.preventDefault();

    const id = btn.dataset.id;
    if (!id) return;

    const data = await postJson(`/cart/remove/${id}`, {});
    if (data && data.ok) {
      renderSideCart(data.cart, data.sum, data.count);

      // если мы на странице cart — уберём строку и обновим суммы
      const row = document.querySelector(`li.item[data-id="${id}"]`);
      if (row) row.remove();

      const sumTotal = document.getElementById('sumTotal');
      const sumGrand = document.getElementById('sumGrand');
      if (sumTotal) sumTotal.textContent = money(data.sum);
      if (sumGrand) sumGrand.textContent = money(data.sum);
    }
  });

  // ----------------------------
  // Remove (cart page крестик)
  // ----------------------------
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-cart-remove-page');
    if (!btn) return;

    e.preventDefault();

    const id = btn.dataset.id;
    if (!id) return;

    const data = await postJson(`/cart/remove/${id}`, {});
    if (data && data.ok) {
      renderSideCart(data.cart, data.sum, data.count);

      const row = btn.closest('li.item');
      if (row) row.remove();

      const sumTotal = document.getElementById('sumTotal');
      const sumGrand = document.getElementById('sumGrand');
      if (sumTotal) sumTotal.textContent = money(data.sum);
      if (sumGrand) sumGrand.textContent = money(data.sum);
    }
  });

})();
</script>
