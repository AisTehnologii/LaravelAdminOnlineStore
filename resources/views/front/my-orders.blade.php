@extends('front.layouts.app')

@section('title', 'Мои заказы')

@section('content')

{{-- HERO (как на других страницах) --}}
<section class="parallax-thight"
         style="background: transparent url('{{ asset('tiband/img/banners/5.jpg') }}') no-repeat fixed 50% 50px / cover;">
    <div class="container">
        <div class="row">
            <div class="text-left-1">
                <h1>МОИ ЗАКАЗЫ</h1>
                <h4 style="opacity:.85;">
                    Всего: <span>{{ $orders->total() ?? $orders->count() }}</span>
                </h4>
            </div>
        </div>
    </div>
</section>

<section class="section section-margin">
    <div class="container">

        {{-- styles only for this page --}}
        <style>
            .orders-wrap { max-width: 980px; margin: 0 auto; }
            .order-card {
                background:#fff;
                border:1px solid rgba(0,0,0,.06);
                border-radius:14px;
                padding:18px 18px;
                margin-bottom:14px;
                box-shadow: 0 10px 30px rgba(0,0,0,.04);
            }
            .order-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
            .order-left { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
            .order-number { font-weight:700; letter-spacing:.4px; }
            .order-meta { opacity:.75; font-size:12px; }
            .order-badge {
                display:inline-flex; align-items:center; gap:6px;
                padding:6px 10px; border-radius:999px;
                font-size:12px; font-weight:600;
                background:rgba(0,0,0,.06);
            }
            .badge-dot { width:8px; height:8px; border-radius:50%; background:#999; display:inline-block; }

            .badge-new { background: rgba(79, 70, 229, .10); color:#2f2a7a; }
            .badge-new .badge-dot { background:#4f46e5; }

            .badge-paid { background: rgba(16, 185, 129, .12); color:#0f6b4e; }
            .badge-paid .badge-dot { background:#10b981; }

            .badge-cancel { background: rgba(239, 68, 68, .12); color:#7a1b1b; }
            .badge-cancel .badge-dot { background:#ef4444; }

            .order-body { margin-top:12px; display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; }
            .order-sum { font-weight:700; font-size:14px; }
            .order-actions { display:flex; align-items:center; gap:8px; }

            .order-mini {
                margin-top:12px;
                padding-top:12px;
                border-top:1px solid rgba(0,0,0,.06);
                display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;
                font-size:12px; opacity:.85;
            }

            /* modal */
            .modal .modal-content { border-radius:14px; overflow:hidden; }
            .modal .modal-header { border-bottom:1px solid rgba(0,0,0,.06); }
            .modal .modal-title small { display:inline-block; margin-left:10px; opacity:.65; font-size:12px; }
            .order-info-grid {
                display:grid;
                grid-template-columns: 1fr 1fr;
                gap:10px 16px;
                margin-bottom:14px;
                font-size:13px;
            }
            .order-info-grid div { opacity:.9; }
            .order-info-grid strong { opacity:1; }

            @media (max-width: 767px) {
                .order-info-grid { grid-template-columns: 1fr; }
            }
        </style>

        <div class="orders-wrap">

            @if(($orders->count() ?? 0) === 0)
                <div class="order-card" style="text-align:center; opacity:.75;">
                    Заказов пока нет
                </div>
            @else

                @foreach($orders as $o)
                    @php
                        $modalId = 'OrderModal-'.$o->id;

                        $status = strtolower((string)$o->status);
                        $badgeClass = 'order-badge';
                        $statusLabel = $o->status;

                        if (in_array($status, ['new','created'])) {
                            $badgeClass .= ' badge-new';
                            $statusLabel = 'Новый';
                        } elseif (in_array($status, ['paid','success','done','completed'])) {
                            $badgeClass .= ' badge-paid';
                            $statusLabel = 'Оплачен';
                        } elseif (in_array($status, ['cancel','canceled','cancelled'])) {
                            $badgeClass .= ' badge-cancel';
                            $statusLabel = 'Отменён';
                        }

                        $dateStr = optional($o->ordered_at)->format('d.m.Y H:i');
                        $currency = $o->currency ?? 'MDL';
                        $itemsCount = $o->items?->count() ?? 0;
                    @endphp

                    <div class="order-card">
                        <div class="order-head">
                            <div class="order-left">
                                <div>
                                    <div class="order-number">{{ $o->number }}</div>
                                    <div class="order-meta">{{ $dateStr }}</div>
                                </div>

                                <div class="{{ $badgeClass }}">
                                    <span class="badge-dot"></span> {{ $statusLabel }}
                                </div>
                            </div>

                            <div class="order-actions">
                                <div class="order-sum">
                                    {{ number_format((float)$o->grand_total, 2, '.', ' ') }} {{ $currency }}
                                </div>

                                <a href="#"
                                   class="button-3 button-round button-small"
                                   data-toggle="modal"
                                   data-target="#{{ $modalId }}">
                                    ОТКРЫТЬ
                                </a>
                            </div>
                        </div>

                        <div class="order-mini">
                            <div><strong>Позиций:</strong> {{ $itemsCount }}</div>
                            <div><strong>Email:</strong> {{ $o->customer_email ?? auth()->user()->email }}</div>
                            <div><strong>Имя:</strong> {{ $o->customer_name ?? auth()->user()->name }}</div>
                        </div>
                    </div>

                    {{-- MODAL --}}
                    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                                    <h4 class="modal-title" id="{{ $modalId }}Label">
                                        Заказ {{ $o->number }}
                                        <small>{{ $dateStr }}</small>
                                    </h4>
                                </div>

                                <div class="modal-body">

                                    <div class="order-info-grid">
                                        <div><strong>Статус:</strong> {{ $statusLabel }}</div>
                                        <div><strong>Сумма:</strong> {{ number_format((float)$o->grand_total, 2, '.', ' ') }} {{ $currency }}</div>

                                        <div><strong>Покупатель:</strong> {{ $o->customer_name ?? auth()->user()->name }}</div>
                                        <div><strong>Email:</strong> {{ $o->customer_email ?? auth()->user()->email }}</div>

                                        @if(!empty($o->customer_phone))
                                            <div><strong>Телефон:</strong> {{ $o->customer_phone }}</div>
                                        @endif

                                        @if(!empty($o->external_id))
                                            <div><strong>External ID:</strong> {{ $o->external_id }}</div>
                                        @endif
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Товар</th>
                                                <th style="width:120px;">Кол-во</th>
                                                <th style="width:140px;">Цена</th>
                                                <th style="width:140px;">Сумма</th>
                                            </tr>
                                            </thead>
                                          <tbody>
@foreach($o->items as $it)
    @php
        $title = $it->product?->title
            ?: data_get($o->raw, 'cart.'.(string)$it->product_id.'.title')
            ?: ('Товар #'.$it->product_id);
    @endphp

    <tr>
        <td>{{ $title }}</td>
        <td>{{ number_format((float)$it->quantity, 3, '.', ' ') }}</td>
        <td>{{ number_format((float)$it->unit_amount, 2, '.', ' ') }}</td>
        <td>{{ number_format((float)$it->total_amount, 2, '.', ' ') }}</td>
    </tr>
@endforeach
</tbody>

                                            <tfoot>
                                            <tr>
                                                <td colspan="3" style="text-align:right;"><strong>ИТОГО:</strong></td>
                                                <td><strong>{{ number_format((float)$o->grand_total, 2, '.', ' ') }}</strong></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    @if(!empty($o->comment))
                                        <div style="margin-top:10px; padding:12px; border-radius:12px; background:rgba(0,0,0,.04);">
                                            <strong>Комментарий:</strong> {{ $o->comment }}
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- pagination (если paginator) --}}
                @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div style="margin-top:18px;">
                        {{ $orders->links() }}
                    </div>
                @endif

            @endif

        </div>
    </div>
</section>
@endsection
