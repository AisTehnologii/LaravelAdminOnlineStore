{{-- resources/views/front/products.blade.php --}}
@extends('front.layouts.app')

@section('title', 'Товары')

@section('content')
@php
  $couponPercent = (int) (session('coupon.percent') ?? 0);
@endphp

<section class="parallax-thight" style="background: transparent url('{{ asset('tiband/img/banners/5.jpg') }}') no-repeat fixed 50% 50px / cover ;">
    <div class="container">
        <div class="row">
            <div class="text-left-1">
                <h1>ТОВАРЫ</h1>
                <h4>У Вас добавлено <span data-cart-count>{{ $itemsCount ?? 0 }}</span> товара</h4>
            </div>
        </div>
    </div>
</section>

<section class="section section-margin">
    <div class="container">
        <div class="row">

            {{-- LEFT GRID --}}
            <div class="col-md-9">

                @php
                    // Изначально показываем ВСЕ товары. Фильтр применяем только когда apply=1
                    $qs = request()->except(['page']);
                    if (!request()->has('apply')) {
                        unset($qs['min'], $qs['max'], $qs['apply']);
                    }
                @endphp

                {{-- SORT / ORDER --}}
                <nav class="pagination no-margin blog-margin-bottom">
                    <div class="sorting">
                        <a class="drop-toggle-2" href="#">Сортировка <i class="fa fa-angle-down"></i></a>
                        <ul class="drop-menu-2">
                            <li><a href="{{ route('products.index', array_merge($qs, ['sort'=>'position'])) }}">По позиции</a></li>
                            <li><a href="{{ route('products.index', array_merge($qs, ['sort'=>'price'])) }}">По цене</a></li>
                            <li><a href="{{ route('products.index', array_merge($qs, ['sort'=>'newest'])) }}">По дате</a></li>
                        </ul>
                    </div>

                    <div class="sorting">
                        <a class="drop-toggle-2" href="#">Упорядочить <i class="fa fa-angle-down"></i></a>
                        <ul class="drop-menu-2">
                            <li><a href="{{ route('products.index', array_merge($qs, ['dir'=>'asc'])) }}">Возрастанию</a></li>
                            <li><a href="{{ route('products.index', array_merge($qs, ['dir'=>'desc'])) }}">Убыванию</a></li>
                        </ul>
                    </div>

                    @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <p>
                            Показано
                            {{ ($products->currentPage()-1)*$products->perPage()+1 }}
                            -
                            {{ ($products->currentPage()-1)*$products->perPage()+$products->count() }}
                            из {{ $products->total() }}
                        </p>
                    @endif
                </nav>

                {{-- PRODUCTS GRID --}}
                <div class="lowerpad row">
                    @forelse($products ?? [] as $item)
                        @php
                            $announceImage = $item->announce_image_path
                                ? \Illuminate\Support\Facades\Storage::url($item->announce_image_path)
                                : asset('tiband/img/products/1.jpg');

                            $hasSale = !empty($item->sale_price) && (float)$item->sale_price > 0;
                            $modalId = 'productModal-' . $item->id;

                            $gallery = $item->relationLoaded('images') ? $item->images : ($item->images ?? collect());

                            $shortTitle = $item->announce_title ?: $item->title;
                            $shortDesc  = $item->announce_description ?: '';
                            $detailDesc = $item->description ?: '';
                            $detailExtra = $item->description_extra ?: '';
                        @endphp

                        <div class="col-lg-4 col-sm-6 col-xs-12">
                            <div class="isotope-info">
                                <img class="img-responsive" src="{{ $announceImage }}" alt=""/>

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
                                <h5>{{ $shortTitle }}</h5>

                               @php
  $old = $hasSale ? (float)$item->price : null;                 // зачеркнуть (если sale)
  $base = $hasSale ? (float)$item->sale_price : (float)$item->price; // базовая цена (уже sale если есть)
  $final = $couponPercent > 0 ? round($base * (100 - $couponPercent) / 100, 2) : $base;
@endphp

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
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL (catalog item) --}}
                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label">
                            <div class="modal-dialog modal-lg" id="quickview-modal">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row lowerpad">

                                            {{-- LEFT: gallery --}}
                                            <div class="col-lg-5 col-sm-5 col-xs-12">
                                                <div class="sp-wrap">
                                                    @if($gallery && $gallery->count())
                                                        @foreach($gallery as $img)
                                                            @php $imgUrl = \Illuminate\Support\Facades\Storage::url($img->image_path); @endphp
                                                            <a href="{{ $imgUrl }}"><img src="{{ $imgUrl }}" alt=""></a>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ $announceImage }}"><img src="{{ $announceImage }}" alt=""></a>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- RIGHT: info --}}
                                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                                <div class="shop-item-label big">
                                                    <h5>{{ $item->title }}</h5>

                                                    @php
  $old = $hasSale ? (float)$item->price : null;
  $base = $hasSale ? (float)$item->sale_price : (float)$item->price;
  $final = $couponPercent > 0 ? round($base * (100 - $couponPercent) / 100, 2) : $base;
@endphp

<span>
  @if($old)
    <del class="reduction">{{ number_format($old, 2, '.', ' ') }}</del>
  @endif

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
    <span style="margin-left:6px;">
      {{ number_format($base, 2, '.', ' ') }}
    </span>
  @endif
</span>


                                                    <div class="rating">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    </div>

                                                    {{-- обычное описание --}}
                                                    @if($shortDesc !== '')
                                                        <div style="margin-top:12px;">
                                                            <p>{{ $shortDesc }}</p>
                                                        </div>
                                                    @endif

                                                    {{-- детальное описание --}}
                                                    @if($detailDesc !== '')
                                                        <div style="margin-top:10px;">
                                                            <p>{{ $detailDesc }}</p>
                                                        </div>
                                                    @endif

                                                    {{-- доп. описание --}}
                                                    @if($detailExtra !== '')
                                                        <div style="margin-top:10px;">
                                                            <p>{{ $detailExtra }}</p>
                                                        </div>
                                                    @endif

                                                    {{-- qty + add --}}
                                                    <div style="margin-top:14px;">
                                                        <form class="ammount js-qty-form">
                                                            <button type="button" class="js-qty-minus">-</button>
                                                            <input id="qty-{{ $item->id }}" type="text" class="js-qty-input" value="1"/>
                                                            <button type="button" class="js-qty-plus">+</button>

                                                            <button type="button"
                                                                    class="button-2 button-xsmall js-add-to-cart"
                                                                    data-id="{{ $item->id }}"
                                                                    data-qty-input="#qty-{{ $item->id }}">
                                                                В КОРЗИНУ
                                                            </button>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="col-xs-12" style="padding:20px;opacity:.7;">
                            Товаров не найдено
                        </div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div style="margin-top:20px;">
                        {{ $products->links() }}
                    </div>
                @endif

            </div>

            {{-- RIGHT SIDEBAR --}}
            <aside class="col-md-3 sidebar sidebar-right">

                {{-- PRICE FILTER (исправлено: без класса .bar от Tiband) --}}
                <section id="filter" class="widget widget-filter">
                    <h5>ФИЛЬТР ПО ЦЕНЕ</h5>

                    <form id="priceFilterForm" method="GET" action="{{ route('products.index') }}">
                        <input type="hidden" name="apply" value="1">
                        <input type="hidden" name="sort" value="{{ $sort ?? 'position' }}">
                        <input type="hidden" name="dir" value="{{ $dir ?? 'asc' }}">

                        <input type="hidden" name="min" id="minInput" value="{{ $appliedMin ?? '' }}">
                        <input type="hidden" name="max" id="maxInput" value="{{ $appliedMax ?? '' }}">

                        <div class="volume">
                            <div class="bar-containter">
                                <div id="priceBar"
                                     data-min="{{ (float)($priceMin ?? 0) }}"
                                     data-max="{{ (float)($priceMax ?? 0) }}"
                                     data-cur-min="{{ $appliedMin ?? '' }}"
                                     data-cur-max="{{ $appliedMax ?? '' }}"
                                     style="position:relative; height:6px; background:#d8d8d8; border-radius:4px;">

                                    <div id="barFill"
                                         style="position:absolute; height:100%; background:#4b3f5e; border-radius:4px;"></div>

                                    <span id="handleMin"
                                          style="position:absolute; top:50%; transform:translate(-50%,-50%); width:14px; height:14px; border-radius:50%; background:#4b3f5e; cursor:pointer;"></span>

                                    <span id="handleMax"
                                          style="position:absolute; top:50%; transform:translate(-50%,-50%); width:14px; height:14px; border-radius:50%; background:#4b3f5e; cursor:pointer;"></span>
                                </div>
                            </div>

                            <span>Цена:</span>
                            <span class="min" id="minLabel">{{ $appliedMin ?? $priceMin }}</span>
                            <span> - </span>
                            <span class="max" id="maxLabel">{{ $appliedMax ?? $priceMax }}</span>

                            <button type="submit" class="button-3 button-round button-small pull-right">
                                ПРИМЕНИТЬ
                            </button>
                        </div>
                    </form>
                </section>

                {{-- POPULAR PRODUCTS --}}
                <section id="featured-products" class="widget widget-featured-products">
                    <h5>ПОПУЛЯРНЫЕ ТОВАРЫ</h5>

                    <ul>
                        @forelse($popularProducts ?? [] as $p)
                            @php
                                $img = $p->announce_image_path
                                    ? \Illuminate\Support\Facades\Storage::url($p->announce_image_path)
                                    : asset('tiband/img/products/1.jpg');

                                $hasSale = !empty($p->sale_price) && (float)$p->sale_price > 0;
                                $effective = $hasSale ? (float)$p->sale_price : (float)$p->price;

                                $modalId = 'popularModal-' . $p->id;

                                $shortTitle = $p->announce_title ?: $p->title;
                                $shortDesc  = $p->announce_description ?: '';
                                $detailDesc = $p->description ?: '';
                                $detailExtra = $p->description_extra ?: '';

                                $pGallery = $p->relationLoaded('images') ? $p->images : ($p->images ?? collect());
                            @endphp

                            <li>
                                <div class="whitefade">
                                    <a href="#" data-toggle="modal" data-target="#{{ $modalId }}">
                                        <img src="{{ $img }}" alt=""/>
                                    </a>
                                </div>
                                <div class="shop-item-label">
                                    <h5>
                                        <a href="#" data-toggle="modal" data-target="#{{ $modalId }}">
                                            {{ $shortTitle }}
                                        </a>
                                    </h5>
                                    <span>
  @if($old)
    <del class="reduction">{{ number_format($old, 2, '.', ' ') }}</del>
  @endif

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
    <span style="margin-left:6px;">
      {{ number_format($base, 2, '.', ' ') }}
    </span>
  @endif
</span>
                                    <div class="rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>
                                </div>
                            </li>

                            {{-- MODAL (popular item) --}}
                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label">
                                <div class="modal-dialog modal-lg" id="quickview-modal">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row lowerpad">

                                                <div class="col-lg-5 col-sm-5 col-xs-12">
                                                    <div class="sp-wrap">
                                                        @if($pGallery && $pGallery->count())
                                                            @foreach($pGallery as $imgRow)
                                                                @php $imgUrl = \Illuminate\Support\Facades\Storage::url($imgRow->image_path); @endphp
                                                                <a href="{{ $imgUrl }}"><img src="{{ $imgUrl }}" alt=""></a>
                                                            @endforeach
                                                        @else
                                                            <a href="{{ $img }}"><img src="{{ $img }}" alt=""></a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-lg-7 col-sm-7 col-xs-12">
                                                    <div class="shop-item-label big">
                                                        <h5>{{ $p->title }}</h5>

                                                        @php
  $old = $hasSale ? (float)$item->price : null;
  $base = $hasSale ? (float)$item->sale_price : (float)$item->price;
  $final = $couponPercent > 0 ? round($base * (100 - $couponPercent) / 100, 2) : $base;
@endphp

<span>
  @if($old)
    <del class="reduction">{{ number_format($old, 2, '.', ' ') }}</del>
  @endif

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
    <span style="margin-left:6px;">
      {{ number_format($base, 2, '.', ' ') }}
    </span>
  @endif
</span>


                                                        <div class="rating">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star-o"></i>
                                                        </div>

                                                        {{-- обычное описание --}}
                                                        @if($shortDesc !== '')
                                                            <div style="margin-top:12px;">
                                                                <p>{{ $shortDesc }}</p>
                                                            </div>
                                                        @endif

                                                        {{-- детальное описание --}}
                                                        @if($detailDesc !== '')
                                                            <div style="margin-top:10px;">
                                                                <p>{{ $detailDesc }}</p>
                                                            </div>
                                                        @endif

                                                        {{-- доп. описание --}}
                                                        @if($detailExtra !== '')
                                                            <div style="margin-top:10px;">
                                                                <p>{{ $detailExtra }}</p>
                                                            </div>
                                                        @endif

                                                        {{-- qty + add --}}
                                                        <div style="margin-top:14px;">
                                                            <form class="ammount js-qty-form">
                                                                <button type="button" class="js-qty-minus">-</button>
                                                                <input id="qty-pop-{{ $p->id }}" type="text" class="js-qty-input" value="1"/>
                                                                <button type="button" class="js-qty-plus">+</button>

                                                                <button type="button"
                                                                        class="button-2 button-xsmall js-add-to-cart"
                                                                        data-id="{{ $p->id }}"
                                                                        data-qty-input="#qty-pop-{{ $p->id }}">
                                                                    В КОРЗИНУ
                                                                </button>
                                                            </form>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <li style="opacity:.7;">Нет товаров</li>
                        @endforelse
                    </ul>
                </section>

            </aside>

        </div>
    </div>
</section>

{{-- JS: 2 handles range slider (левый=min, правый=max) --}}
<script>
(function () {
  const bar = document.getElementById('priceBar');
  if (!bar) return;

  const fill = document.getElementById('barFill');
  const hMin = document.getElementById('handleMin');
  const hMax = document.getElementById('handleMax');

  const minLabel = document.getElementById('minLabel');
  const maxLabel = document.getElementById('maxLabel');
  const minInput = document.getElementById('minInput');
  const maxInput = document.getElementById('maxInput');

  const minAllowed = parseFloat(bar.dataset.min || '0');
  const maxAllowed = parseFloat(bar.dataset.max || '0');

  let curMin = (bar.dataset.curMin !== '') ? parseFloat(bar.dataset.curMin) : minAllowed;
  let curMax = (bar.dataset.curMax !== '') ? parseFloat(bar.dataset.curMax) : maxAllowed;

  function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }

  function valueToPct(v){
    if (maxAllowed === minAllowed) return 0;
    return ((v - minAllowed) / (maxAllowed - minAllowed)) * 100;
  }

  function pctToValue(pct){
    return minAllowed + (maxAllowed - minAllowed) * (pct / 100);
  }

  function sync(){
    curMin = clamp(curMin, minAllowed, maxAllowed);
    curMax = clamp(curMax, minAllowed, maxAllowed);
    if (curMin > curMax) curMin = curMax;

    const pMin = valueToPct(curMin);
    const pMax = valueToPct(curMax);

    hMin.style.left = pMin + '%';
    hMax.style.left = pMax + '%';

    fill.style.left = pMin + '%';
    fill.style.width = Math.max(0, pMax - pMin) + '%';

    minLabel.textContent = Math.round(curMin);
    maxLabel.textContent = Math.round(curMax);

    minInput.value = String(Math.round(curMin));
    maxInput.value = String(Math.round(curMax));
  }

  let dragging = null; // 'min' | 'max'

  function clientX(e){
    if (e.touches && e.touches.length) return e.touches[0].clientX;
    return e.clientX;
  }

  function onDown(which, e){
    e.preventDefault();
    dragging = which;

    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onUp);
    document.addEventListener('touchmove', onMove, { passive:false });
    document.addEventListener('touchend', onUp);
  }

  function onMove(e){
    if (!dragging) return;
    e.preventDefault();

    const rect = bar.getBoundingClientRect();
    const x = clientX(e) - rect.left;
    let pct = (x / rect.width) * 100;
    pct = clamp(pct, 0, 100);

    let val = pctToValue(pct);

    if (dragging === 'min') {
      val = Math.min(val, curMax);
      curMin = val;
    } else {
      val = Math.max(val, curMin);
      curMax = val;
    }

    sync();
  }

  function onUp(){
    dragging = null;
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
    document.removeEventListener('touchmove', onMove);
    document.removeEventListener('touchend', onUp);
  }

  hMin.addEventListener('mousedown', onDown.bind(null, 'min'));
  hMax.addEventListener('mousedown', onDown.bind(null, 'max'));
  hMin.addEventListener('touchstart', onDown.bind(null, 'min'), { passive:false });
  hMax.addEventListener('touchstart', onDown.bind(null, 'max'), { passive:false });

  bar.addEventListener('click', function(e){
    if (e.target === hMin || e.target === hMax) return;

    const rect = bar.getBoundingClientRect();
    const x = clientX(e) - rect.left;
    let pct = (x / rect.width) * 100;
    pct = clamp(pct, 0, 100);

    const val = pctToValue(pct);
    const distMin = Math.abs(val - curMin);
    const distMax = Math.abs(val - curMax);

    if (distMin <= distMax) curMin = Math.min(val, curMax);
    else curMax = Math.max(val, curMin);

    sync();
  });

  sync();
})();
</script>

@endsection
