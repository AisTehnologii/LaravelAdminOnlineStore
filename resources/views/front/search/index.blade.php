@extends('front.layouts.app')

@section('title', 'Поиск')

@section('content')
<section class="section-bar-1 section-margin" style="padding-bottom: 20px !important;">
    <div class="container">
        <div class="callout-box-1">
            <div class="box-text">
                <h3>ПОИСК</h3>
            </div>
            <div class="box-text-2">
                <output>Запрос: {{ $q }}</output>
            </div>
        </div>
    </div>
</section>

<section class="section section-margin">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <h4 style="margin-bottom:15px;">Товары</h4>

                @if($products->count())
                    <ul style="margin-bottom:35px;">
                        @foreach($products as $p)
                            <li style="margin:8px 0;">
                                <a href="{{ url('/products') }}?product={{ $p->id }}">
                                    {{ $p->title ?? $p->announce_title ?? ('Product #'.$p->id) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="opacity:.7;">Товары не найдены</p>
                @endif

                <h4 style="margin:30px 0 15px;">Новости</h4>

                @if($news->count())
                    <ul>
                        @foreach($news as $n)
                            <li style="margin:8px 0;">
                                <a href="{{ route('news.show', $n->id) }}">
                                    {{ $n->title ?? ('News #'.$n->id) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="opacity:.7;">Новости не найдены</p>
                @endif
            </div>

        </div>
    </div>
</section>
@endsection
