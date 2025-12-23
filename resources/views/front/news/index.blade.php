@extends('front.layouts.app')

@section('title', 'Новости')

@section('content')
<section class="section-bar-1 section-margin" style="padding-bottom: 20px !important;">
    <div class="container">
        <div class="callout-box-1">
            <div class="box-text">
                <h3>КАТЕГОРИИ НОВОСТЕЙ</h3>
            </div>
            <div class="box-text-2">
                <output>Главная / Новости</output>
            </div>
        </div>
    </div>
</section>

<section class="section section-margin">
    <div class="container">
        <div class="row">

            <div class="col-lg-9">
                <div class="row">

                    @forelse($news as $item)
                        <div class="col-lg-6 col-sm-6 col-xs-12 blog-margin-top">
                            <article class="blog blog-grid">

                                {{-- image --}}
                                @if(!empty($item->image_path))
                                    <img class="img-responsive" src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title ?? '' }}">
                                @else
                                    <div style="height:240px;background:#222;"></div>
                                @endif

                                <header>
                                    <a href="{{ route('news.show', $item->id) }}">
                                        <h4>{{ $item->title ?? '' }}</h4>
                                    </a>

                                    {{-- дата: день/месяц в description/description_2 --}}
                                    <span>
                                        {{ trim(($item->description ?? '').' '.($item->description_2 ?? '')) }}
                                    </span>
                                </header>

                                {{-- короткий текст --}}
                                @php
                                    $excerpt = $item->subtitle
                                        ?? $item->announce_description
                                        ?? $item->short_text
                                        ?? null;
                                @endphp

                                @if(!empty($excerpt))
                                    <p>{{ $excerpt }}</p>
                                @else
                                    <p style="opacity:.7;">&nbsp;</p>
                                @endif

                                <footer>
                                    <a href="{{ route('news.show', $item->id) }}">ЧИТАТЬ</a>
                                </footer>
                            </article>
                        </div>
                    @empty
                        <div class="col-md-12">
                            <p style="padding:20px 0; opacity:.7;">Новостей пока нет</p>
                        </div>
                    @endforelse

                </div>

                {{-- Pagination --}}
                @if($news->hasPages())
                    <nav class="pagination" style="margin-top:25px;">
                        {{-- prev --}}
                        @if($news->onFirstPage())
                            <span class="prev" style="opacity:.4;"></span>
                        @else
                            <a class="prev" href="{{ $news->previousPageUrl() }}"></a>
                        @endif

                        {{-- next --}}
                        @if($news->hasMorePages())
                            <a class="next" href="{{ $news->nextPageUrl() }}"></a>
                        @else
                            <span class="next" style="opacity:.4;"></span>
                        @endif
                    </nav>
                @endif
            </div>

            {{-- SIDEBAR --}}
            <aside class="col-md-3 sidebar sidebar-right">

                <section id="recent-news" class="widget">
                    <h5>ПОСЛЕДНИЕ НОВОСТИ</h5>
                    <ul>
                        @foreach($latestNews as $n)
                            <li>
                                <h6>
                                    <a href="{{ route('news.show', $n->id) }}">{{ $n->title ?? '' }}</a>
                                </h6>
                                <p>{{ trim(($n->description ?? '').' '.($n->description_2 ?? '')) }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>

                

                {{-- фотогалерея — статик --}}
                <section id="recent-projects" class="widget widget-recent-projects">
                    <h5>ФОТОГАЛЕРЕЯ</h5>
                    <div class="imgbox">
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/1.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/2.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/3.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/4.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/5.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/6.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/7.jpg') }}" alt=""></a></div>
                        <div class="whitefade"><a href="#"><img src="{{ asset('tiband/img/blog/8.jpg') }}" alt=""></a></div>
                    </div>
                </section>

                {{-- ЛУЧШИЕ ОТЗЫВЫ — ДИНАМИКА --}}
                <section id="recent-comments" class="widget widget-recent-projects">
                    <h5>ЛУЧШИЕ ОТЗЫВЫ</h5>

                    @if(isset($reviews) && $reviews->count())
                        <ul>
                            @foreach($reviews as $item)
                                @php
                                    // Автор + должность/доп. строка как на главной
                                    $authorLine = trim(collect([$item->subtitle ?? null, $item->description_2 ?? null])->filter()->join(', '));

                                    // Короткий текст отзыва: description (как на главной)
                                    $text = $item->description ?? null;

                                    // Заголовок отзыва: title
                                    $title = $item->title ?? null;

                                    $href = !empty($item->link) ? $item->link : null;
                                @endphp

                                <li>
                                    <h6>
                                        @if($href)
                                            <a href="{{ $href }}">{{ $title ?: 'Отзыв' }}</a>
                                        @else
                                            <a href="#">{{ $title ?: 'Отзыв' }}</a>
                                        @endif
                                    </h6>

                                    @if(!empty($authorLine))
                                        <p style="margin-bottom:6px;">{{ $authorLine }}</p>
                                    @endif

                                    @if(!empty($text))
                                        <p style="opacity:.85;">{{ $text }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p style="opacity:.7;">Пока нет отзывов</p>
                    @endif
                </section>

            </aside>

        </div>
    </div>
</section>
@endsection
