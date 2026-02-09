<!-- =============== FOOTER ============ -->
@php
    use Illuminate\Support\Facades\Schema;
    use App\Models\ContentSection;
    use App\Models\Product;
    use App\Models\PromoBlock;

    $locale = session('locale', app()->getLocale());

    // -------------------------
    // Latest Products (5)
    // -------------------------
    $catalogSectionId = ContentSection::query()
        ->where('slug', 'catalog')
        ->where('type', 'product')
        ->when(Schema::hasColumn('content_sections', 'is_active'), fn($q) => $q->where('is_active', 1))
        ->value('id');

    $latestProductsQ = Product::query();

    if (Schema::hasColumn('products', 'locale')) {
        $latestProductsQ->where('locale', $locale);
    }
    if (Schema::hasColumn('products', 'is_active')) {
        $latestProductsQ->where('is_active', 1);
    }
    if (!empty($catalogSectionId) && Schema::hasColumn('products', 'section_id')) {
        $latestProductsQ->where('section_id', $catalogSectionId);
    }

    if (Schema::hasColumn('products', 'position')) {
        $latestProductsQ->orderBy('position');
    }
    $latestProductsQ->orderByDesc('id');

    $latestProducts = $latestProductsQ->limit(5)->get();

    // -------------------------
    // Latest News (5) from promo_blocks in section "news"
    // -------------------------
    $newsSectionId = ContentSection::query()
        ->where('slug', 'news')
        ->when(Schema::hasColumn('content_sections', 'is_active'), fn($q) => $q->where('is_active', 1))
        ->value('id');

    $latestNewsQ = PromoBlock::query();

    if (!empty($newsSectionId) && Schema::hasColumn('promo_blocks', 'section_id')) {
        $latestNewsQ->where('section_id', $newsSectionId);
    }
    if (Schema::hasColumn('promo_blocks', 'locale')) {
        $latestNewsQ->where('locale', $locale);
    }
    // is_active в promo_blocks может отсутствовать — НЕ фильтруем

    if (Schema::hasColumn('promo_blocks', 'position')) {
        $latestNewsQ->orderBy('position');
    }
    $latestNewsQ->orderByDesc('id');

    $latestNews = $latestNewsQ->limit(5)->get();
@endphp

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
                            @forelse($latestProducts as $p)
                                <li>
                                    <h6>
                                        <a href="{{ route('products.index') }}">
                                            {{ $p->announce_title ?: $p->title ?: 'Товар' }}
                                        </a>
                                    </h6>
                                </li>
                            @empty
                                <li><h6 style="opacity:.7;">Нет товаров</h6></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-3 col-xs-12">
                    <div class="widget la-hover">
                        <h5>Последние новости</h5>
                        <ul>
                            @forelse($latestNews as $n)
                                <li>
                                    <h6>
                                        <a href="{{ route('news.show', $n->id) }}">
                                            {{ $n->title ?: 'Новость' }}
                                        </a>
                                    </h6>
                                </li>
                            @empty
                                <li><h6 style="opacity:.7;">Нет новостей</h6></li>
                            @endforelse
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
@php
    $cart = session('cart', []);
    $cartCount = array_sum(array_column($cart, 'qty'));

    $couponPercent = (int) (session('coupon.percent') ?? 0);
    $couponCode = (string) (session('coupon.code') ?? '');

    $cartSum = 0;        // "как было" (без купона)
    $cartSumFinal = 0;   // "как стало" (с купоном)
    $cartDiscount = 0;

    foreach ($cart as $row) {
        $qty = (int)($row['qty'] ?? 0);

        // base_price появляется после /coupon/apply (у тебя так и сделано)
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

        $lineBase  = round($baseUnit * $qty, 2);
        $lineFinal = round($finalUnit * $qty, 2);

        $cartSum += $lineBase;
        $cartSumFinal += $lineFinal;
    }

    $cartSum = round($cartSum, 2);
    $cartSumFinal = round($cartSumFinal, 2);

    if ($couponPercent > 0) {
        $cartDiscount = max(0, round($cartSum - $cartSumFinal, 2));
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

                    $lineBase  = round($baseUnit * $qty, 2);
                    $lineFinal = round($finalUnit * $qty, 2);

                    $img = $row['image'] ?: asset('tiband/img/products/1.jpg');
                @endphp

                <li data-product-id="{{ $row['product_id'] }}">
                    <a href="#" class="close-button js-cart-remove" data-id="{{ $row['product_id'] }}">X</a>

                    <a href="#">
                        {{ $row['title'] }}

                        <output>
                            {{ $qty }} шт,
                            @if($couponPercent > 0)
                                <del style="opacity:.65; margin-right:6px;">
                                    {{ number_format($lineBase, 2, '.', ' ') }}
                                </del>
                                {{ number_format($lineFinal, 2, '.', ' ') }}
                            @else
                                {{ number_format($lineFinal, 2, '.', ' ') }}
                            @endif
                        </output>
                    </a>

                    <img src="{{ $img }}" alt=""/>
                </li>
            @empty
                <li style="padding:10px 0; opacity:.7;">Корзина пуста</li>
            @endforelse
        </ul>

        {{-- Итоги --}}
        @if($couponPercent > 0 && $cartCount > 0)
            <output style="display:block; margin-top:10px; opacity:.85;">
                КУПОН: <strong>{{ $couponCode ?: '—' }}</strong> (-{{ $couponPercent }}%)
            </output>

            <output style="display:block; margin-top:6px; opacity:.85;">
                СЭКОНОМИЛИ: <strong>{{ number_format($cartDiscount, 2, '.', ' ') }}</strong>
            </output>

            <output id="sideCartTotal" style="display:block; margin-top:8px;">
                ИТОГО: <strong>{{ number_format($cartSumFinal, 2, '.', ' ') }}</strong>
            </output>
        @else
            <output id="sideCartTotal">ИТОГО: {{ number_format($cartSumFinal, 2, '.', ' ') }}</output>
        @endif

        <hr/>

        <div class="side-menu-button">
            <a href="{{ route('cart.index') }}">КОРЗИНА</a>
        </div>
        <div class="side-menu-button">
            <a href="{{ route('cart.index') }}">ОПЛАТИТЬ</a>
        </div>

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

{{-- ===== Купон ===== --}}
<div id="SubscribePopup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="background: transparent url('{{ asset('tiband/img/modal1.jpg') }}') repeat scroll 0% 0% / cover ;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">У ВАС ЕСТЬ СКИДОЧНЫЙ КУПОН?</h4>
            </div>

            <div class="modal-body">
                <p>ВВЕДИТЕ ЕГО ЗЕСЬ И СЕЙЧАС!</p>

                {{-- ✅ ВАЖНО: чтобы НЕ было POST / --}}
                <form class="subscribe-form-popup"
                      id="couponForm"
                      method="POST"
                      action="javascript:void(0);">
                    @csrf

                    <input name="code"
                           class="subscribe-popup"
                           placeholder="ВАШ КУПОН"
                           type="text"
                           autocomplete="off">

                    <button class="button-1 button-medium" type="submit">
                        ПРОВЕРИТЬ!
                    </button>

                    <div id="couponMsg" style="margin-top:10px; font-size:14px;"></div>

                    <div id="couponActive"
                         style="display:none; margin-top:12px; padding:10px 12px; border-radius:10px; background:rgba(0,0,0,.45); color:#fff;">
                        <div style="font-size:13px; opacity:.85;">Активен купон:</div>
                        <div style="margin-top:4px; font-weight:700;">
                            <span id="couponActiveCode">—</span>
                            <span style="opacity:.85;">(</span><span id="couponActivePercent">0</span><span style="opacity:.85;">%)</span>
                        </div>

                        <button type="button" id="couponDeactivateBtn"
                                class="button-1 button-medium"
                                style="margin-top:10px; width:100%; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25);">
                            УБРАТЬ КУПОН
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- ===== Заказ (НЕ form!) ===== --}}
<div id="CookiePopup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ВЫ ДЕЙСТВИТЕЛЬНО ХОТИТЕ СДЕЛАТЬ ЗАКАЗ?</h4>
            </div>

            <div class="modal-body">
                <p>Согласно предыдущему, CTR оправдывает потребительский бренд...</p>

                {{-- ✅ было form -> submit -> POST / --}}
                <div class="subscribe-form-popup">
                    <button class="button-1 button-medium" type="button" data-dismiss="modal">
                        НЕТ, Я ПЕРЕДУМАЛ
                    </button>

                    <a class="button-1 button-medium" href="{{ route('cart.index') }}">
                        КОНЕЧНО, ДА!
                    </a>
                </div>
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








































{{-- СКРИПТ КОРЗИНЫ НЕ ТРОГАЮ --}}
{{-- ✅ CART SCRIPT (исправлен): 
   1) + / - НЕ меняют qty сами — берут значение из input и отправляют на /cart/update/{id}
   2) Итоговые суммы ВСЕГДА с учетом купона (sumGrand + sideCartTotal)
   3) Купон-скрипт НЕ ломается: после apply обновляем глобальный купон и перерисовываем суммы без reload
--}}

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
    return x.toFixed(2);
  }

  // ---------- COUPON helpers ----------
  function couponPercent() {
    const c = window.__activeCoupon || window.activeCoupon || null;
    const p = Number(c?.percent || 0);
    return (Number.isFinite(p) && p > 0) ? p : 0;
  }

  function discounted(sum) {
    const s = Number(sum || 0);
    const p = couponPercent();
    if (!p) return s;
    return s - (s * p / 100);
  }

  function renderTotals(sum) {
    // sum = подытог без скидки (как приходит с сервера)
    const sumEl = document.getElementById('sumTotal');
    if (sumEl) sumEl.textContent = money(sum);

    const grandEl = document.getElementById('sumGrand');
    if (grandEl) grandEl.textContent = money(discounted(sum));

    const sideEl = document.getElementById('sideCartTotal');
    if (sideEl) sideEl.textContent = 'ИТОГО: ' + money(discounted(sum));
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

    // ✅ важно: side итого с учетом купона
    renderTotals(sum);

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

      // строку в мини-корзине оставляем без скидки (как и было), скидка — в ИТОГО
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
  // +/- (НЕ меняем qty сами)
  // Просто берём qty из input и делаем update как раньше
  // ----------------------------
  document.addEventListener('click', async function (e) {
    const plus = e.target.closest('.js-qty-plus');
    const minus = e.target.closest('.js-qty-minus');
    const btnEl = plus || minus;
    if (!btnEl) return;

    e.preventDefault();

    // анти-двойное срабатывание
    if (btnEl.dataset.cartLock === '1') return;
    btnEl.dataset.cartLock = '1';
    setTimeout(() => { btnEl.dataset.cartLock = '0'; }, 120);

    const container = btnEl.closest('.js-qty-form') || btnEl.closest('.ammount');
    if (!container) return;

    const input = container.querySelector('.js-qty-input');
    if (!input) return;

    // ✅ НЕ инкрементируем/декрементируем
    let v = parseInt(input.value || '1', 10);
    if (Number.isNaN(v) || v < 1) v = 1;
    input.value = v;

    // страница cart — обновляем сервер + суммы
    const row = btnEl.closest('li.item[data-id]');
    if (!row) return;

    const id = row.getAttribute('data-id');
    const data = await postJson(`/cart/update/${id}`, { qty: v });

    if (data && data.ok) {
      // line total в строке (без скидки)
      const line = row.querySelector('.js-line-total');
      if (line) line.textContent = money(data.lineTotal);

      // ✅ суммы (sumTotal и sumGrand + sideCartTotal) с учетом купона
      renderTotals(data.sum);

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

    if (btn.dataset.qtyInput) {
      const el = document.querySelector(btn.dataset.qtyInput);
      const v = parseInt(el?.value || '1', 10);
      qty = (!Number.isNaN(v) && v > 0) ? v : 1;
    } else {
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

      const row = document.querySelector(`li.item[data-id="${id}"]`);
      if (row) row.remove();

      // ✅ суммы с учетом купона
      renderTotals(data.sum);
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

      // ✅ суммы с учетом купона
      renderTotals(data.sum);
    }
  });

  // ✅ при первой загрузке страницы (если sumTotal/sumGrand уже есть) — тоже пересчитаем итог
  // (на случай, если купон активен, а в blade показан subTotal)
  setTimeout(() => {
    const sumEl = document.getElementById('sumTotal');
    const sideEl = document.getElementById('sideCartTotal');
    // если на странице есть сумма — пересчитаем
    if (sumEl) {
      const raw = String(sumEl.textContent || '').replace(/[^\d.]/g, '');
      const s = parseFloat(raw || '0');
      if (!Number.isNaN(s)) renderTotals(s);
    } else if (sideEl) {
      // если только side-cart
      const raw = String(sideEl.textContent || '').replace(/[^\d.]/g, '');
      const s = parseFloat(raw || '0');
      if (!Number.isNaN(s)) renderTotals(s);
    }
  }, 0);

})();
</script>

<script>
(function () {
  function csrf() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  }

  async function getJson(url) {
    const res = await fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok) throw json;
    return json;
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
    const json = await res.json().catch(() => ({}));
    if (!res.ok) throw json;
    return json;
  }

  function setMsg(text, ok) {
    const el = document.getElementById('couponMsg');
    if (!el) return;
    el.textContent = text || '';
    el.style.color = ok ? '#a7ffb3' : '#ffb3b3';
  }

  function renderActiveCoupon(coupon) {
    const box = document.getElementById('couponActive');
    const codeEl = document.getElementById('couponActiveCode');
    const pctEl  = document.getElementById('couponActivePercent');

    const code = String(coupon?.code || '');
    const pct  = Number(coupon?.percent || 0);
    const has  = code && pct > 0;

    if (box) box.style.display = has ? 'block' : 'none';
    if (codeEl) codeEl.textContent = has ? code : '—';
    if (pctEl)  pctEl.textContent  = has ? String(pct) : '0';
  }

  async function refreshCouponUI() {
    try {
      const data = await getJson('/coupon/current');
      renderActiveCoupon(data?.coupon);
      // можно хранить в window, но это уже "кэш", источник истины — сервер
      window.__activeCoupon = data?.coupon || null;
      if (data?.coupon) setMsg('', true);
    } catch (e) {
      renderActiveCoupon(null);
    }
  }

  // ✅ открыли модалку — всегда проверяем купон заново
  document.addEventListener('shown.bs.modal', function (e) {
    if (e.target && e.target.id === 'SubscribePopup') {
      refreshCouponUI();
      setMsg('', true);
    }
  });

  // ✅ на всякий случай и при загрузке (чтобы не пропадало даже без открытия)
  refreshCouponUI();

  // ✅ Apply coupon
  document.addEventListener('submit', async function (e) {
    const form = e.target.closest('#couponForm');
    if (!form) return;

    e.preventDefault();

    const input = form.querySelector('input[name="code"]');
    const code = (input?.value || '').trim();
    if (!code) {
      setMsg('Введите код купона', false);
      return;
    }

    try {
      const data = await postJson('/coupon/apply', { code });

      await refreshCouponUI();
      setMsg(data?.message || 'Купон применён', true);

      // обновим суммы/sidecart если backend это вернул
      if (data?.ok && typeof window.renderSideCart === 'function' && data?.cart) {
        window.renderSideCart(data.cart, data.sum, data.count);
      } else {
        // если нет данных — просто обновим страницу
        window.location.reload();
      }
    } catch (err) {
      setMsg(err?.message || 'Не удалось применить купон', false);
    }
  });

  // ✅ Remove coupon
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('#couponDeactivateBtn');
    if (!btn) return;

    e.preventDefault();

    try {
      const data = await postJson('/coupon/remove', {});
      await refreshCouponUI();
      setMsg(data?.message || 'Купон удалён', true);

      if (data?.ok && typeof window.renderSideCart === 'function') {
        window.renderSideCart(data.cart, data.sum, data.count);
      } else {
        window.location.reload();
      }
    } catch (err) {
      setMsg(err?.message || 'Не удалось убрать купон', false);
    }
  });
})();
</script>































<script>
(function () {
  if (window.__siteSearchInited) return;
  window.__siteSearchInited = true;

  const input = document.getElementById('siteSearchInput');
  const dropdown = document.getElementById('siteSearchDropdown');
  const form = document.getElementById('siteSearchForm');

  if (!input || !dropdown || !form) return;

  // простые inline стили, чтобы работало сразу (можешь потом вынести в css)
  dropdown.style.position = 'absolute';
  dropdown.style.left = '0';
  dropdown.style.right = '0';
  dropdown.style.top = '100%';
  dropdown.style.background = '#111';
  dropdown.style.border = '1px solid rgba(255,255,255,.12)';
  dropdown.style.zIndex = '9999';
  dropdown.style.maxHeight = '320px';
  dropdown.style.overflow = 'auto';

  // контейнер должен быть relative
  const parent = dropdown.parentElement;
  if (parent) parent.style.position = 'relative';

  let timer = null;

  function hide() {
    dropdown.style.display = 'none';
    dropdown.innerHTML = '';
  }

  function show(items) {
    if (!items || !items.length) {
      hide();
      return;
    }

    dropdown.innerHTML = items.map((it) => {
      const badge = it.type === 'product' ? 'ТОВАР' : 'НОВОСТЬ';
      return `
        <a href="${it.url}"
           class="js-search-pick"
           style="display:block;padding:10px 12px;color:#fff;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.06);">
          <span style="opacity:.7;font-size:11px;margin-right:10px;">${badge}</span>
          <span>${escapeHtml(it.title || '')}</span>
        </a>
      `;
    }).join('');

    dropdown.style.display = 'block';
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, (m) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[m]));
  }

  async function fetchSuggest(q) {
    const url = `/search/suggest?q=${encodeURIComponent(q)}`;
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) return [];
    const data = await res.json();
    return (data && data.ok && Array.isArray(data.items)) ? data.items : [];
  }

  input.addEventListener('input', function () {
    const q = input.value.trim();

    clearTimeout(timer);

    if (q.length < 2) {
      hide();
      return;
    }

    timer = setTimeout(async () => {
      const items = await fetchSuggest(q);
      show(items);
    }, 200);
  });

  // клик по подсказке — просто переходим по ссылке
  dropdown.addEventListener('click', function (e) {
    const a = e.target.closest('a.js-search-pick');
    if (!a) return;
    // обычный переход по href
    hide();
  });

  // Enter — открываем страницу результатов
  form.addEventListener('submit', function () {
    hide();
  });

  // клик вне — закрыть
  document.addEventListener('click', function (e) {
    if (e.target === input) return;
    if (dropdown.contains(e.target)) return;
    hide();
  });

  // Esc — закрыть
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') hide();
  });
})();
</script>
<style>
/* ===== Search input ===== */
.site-search-input{
  width: 100%;
  background: #ffffff !important;
  color: #111111 !important;            /* ВАЖНО: текст чёрный */
  border: 1px solid rgba(0,0,0,.15);
  border-radius: 10px;
  padding: 12px 14px;
  outline: none;
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  font-size: 14px;
  line-height: 1.2;
}

.site-search-input::placeholder{
  color: rgba(0,0,0,.45);
}

.site-search-input:focus{
  border-color: rgba(0,0,0,.35);
  box-shadow: 0 10px 26px rgba(0,0,0,.12);
}

/* ===== Dropdown ===== */
.site-search-dropdown{
  position: absolute;
  left: 0;
  right: 0;
  top: calc(100% + 8px);
  background: #ffffff;
  border: 1px solid rgba(0,0,0,.12);
  border-radius: 12px;
  box-shadow: 0 14px 34px rgba(0,0,0,.16);
  overflow: hidden;
  z-index: 9999;
  max-height: 320px;
  overflow-y: auto;
}

/* nice scrollbar */
.site-search-dropdown::-webkit-scrollbar{ width: 10px; }
.site-search-dropdown::-webkit-scrollbar-thumb{
  background: rgba(0,0,0,.18);
  border-radius: 999px;
  border: 2px solid #fff;
}
.site-search-dropdown::-webkit-scrollbar-track{ background: transparent; }

.site-search-item{
  display:flex;
  align-items:center;
  gap:10px;
  padding: 11px 12px;
  text-decoration:none !important;
  color:#111 !important;
  border-bottom: 1px solid rgba(0,0,0,.06);
  transition: background .15s ease;
}

.site-search-item:last-child{ border-bottom:none; }

.site-search-item:hover{
  background: rgba(0,0,0,.04);
}

.site-search-icon{
  width: 28px;
  height: 28px;
  border-radius: 10px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: rgba(0,0,0,.06);
  flex: 0 0 auto;
  font-size: 14px;
}

.site-search-text{
  flex: 1 1 auto;
  min-width: 0;
}

.site-search-title{
  display:block;
  font-weight: 600;
  font-size: 13px;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.site-search-meta{
  display:flex;
  gap:8px;
  align-items:center;
  margin-top: 3px;
}

.site-search-badge{
  display:inline-flex;
  align-items:center;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .06em;
  text-transform: uppercase;
}

.site-search-badge.product{
  background: rgba(0, 140, 255, .10);
  color: #0b5fd3;
}

.site-search-badge.news{
  background: rgba(0, 170, 120, .12);
  color: #087b57;
}

.site-search-hint{
  font-size: 11px;
  color: rgba(0,0,0,.55);
}

/* empty state */
.site-search-empty{
  padding: 12px;
  color: rgba(0,0,0,.55);
  font-size: 13px;
}
</style>
