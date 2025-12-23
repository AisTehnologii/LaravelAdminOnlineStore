{{-- resources/views/front/news/show.blade.php --}}
@extends('front.layouts.app')

@section('title', $post->title ?? 'Новость')

@section('content')

{{-- Верхний бар --}}
<section class="section-bar-1 section-margin" style="padding-bottom: 20px !important;">
    <div class="container">
        <div class="callout-box-1">
            <div class="box-text">
                <h3 style="margin:0;">{{ $post->title ?? 'НОВОСТЬ' }}</h3>
            </div>
            <div class="box-text-2">
                <output>
                    <a href="{{ url('/') }}">Главная</a>
                    / <a href="{{ route('news.index') }}">Новости</a>
                    / {{ $post->title ?? '' }}
                </output>
            </div>
        </div>
    </div>
</section>

@php
    $dateLine = trim(($post->description ?? '').' '.($post->description_2 ?? ''));
    $body = $post->text
        ?? $post->content
        ?? $post->body
        ?? $post->description_long
        ?? $post->description
        ?? null;

    // Для share
    $shareUrl = url()->current();
    $shareTitle = $post->title ?? '';
@endphp

<section class="section section-margin">
    <div class="container">
        <div class="row">

            {{-- MAIN --}}
            <div class="col-lg-9">

                <article class="blog blog-single">

                    {{-- Hero image --}}
                    <div class="news-hero">
                        @if(!empty($post->image_path))
                            <img class="img-responsive" src="{{ asset('storage/'.$post->image_path) }}" alt="{{ $post->title ?? '' }}">
                        @else
                            <div class="news-hero-fallback"></div>
                        @endif

                        {{-- Дата поверх картинки --}}
                        @if($dateLine)
                            <div class="news-date-badge">
                                {{ $dateLine }}
                            </div>
                        @endif
                    </div>

                    {{-- Заголовок + подзаголовок --}}
                    <header class="news-header">
                        <h2 class="news-title">{{ $post->title ?? '' }}</h2>

                        @if(!empty($post->subtitle))
                            <p class="news-subtitle">{{ $post->subtitle }}</p>
                        @endif

                        {{-- Мини-инфо --}}
                        <div class="news-meta">
                            @if($dateLine)
                                <span class="news-meta-item">
                                    <i class="fa fa-calendar"></i>
                                    {{ $dateLine }}
                                </span>
                            @endif

                            <span class="news-meta-item">
                                <i class="fa fa-link"></i>
                                {{ parse_url($shareUrl, PHP_URL_HOST) }}
                            </span>
                        </div>
                    </header>

                    {{-- Контент --}}
                    <div class="news-content">
                        @if(!empty($body))
                            {!! $body !!}
                        @else
                            <p style="opacity:.75; margin:0;">
                                Текст новости пока не заполнен (заполни поле текста в PromoBlock).
                            </p>
                        @endif
                    </div>

                    {{-- Share + Back --}}
                    <div class="news-actions">
                        <a href="{{ route('news.index') }}" class="button-3 button-round button-small">
                            ← НАЗАД К НОВОСТЯМ
                        </a>

                        <div class="news-share">
                            <span class="news-share-label">Поделиться:</span>

                            {{-- Telegram --}}
                            <a class="news-share-btn" target="_blank"
                               href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}"
                               rel="noopener">
                                <i class="fa fa-telegram"></i> Telegram
                            </a>

                            {{-- Facebook --}}
                            <a class="news-share-btn" target="_blank"
                               href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                               rel="noopener">
                                <i class="fa fa-facebook"></i> Facebook
                            </a>

                            {{-- Copy --}}
                            <button type="button" class="news-share-btn news-copy" data-copy="{{ $shareUrl }}">
                                <i class="fa fa-copy"></i> Скопировать
                            </button>
                        </div>
                    </div>

                    {{-- Previous / Next (если передашь из web.php) --}}
                    @if(isset($prevPost) || isset($nextPost))
                        <div class="news-nav">
                            <div class="row">
                                <div class="col-sm-6">
                                    @if(isset($prevPost) && $prevPost)
                                        <a class="news-nav-card" href="{{ route('news.show', $prevPost->id) }}">
                                            <span class="news-nav-kicker">← Предыдущая</span>
                                            <span class="news-nav-title">{{ $prevPost->title ?? '' }}</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    @if(isset($nextPost) && $nextPost)
                                        <a class="news-nav-card text-right" href="{{ route('news.show', $nextPost->id) }}">
                                            <span class="news-nav-kicker">Следующая →</span>
                                            <span class="news-nav-title">{{ $nextPost->title ?? '' }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </article>
            </div>

            {{-- SIDEBAR --}}
            <aside class="col-md-3 sidebar sidebar-right">

                {{-- Последние --}}
                <section id="recent-news" class="widget">
                    <h5>ПОСЛЕДНИЕ НОВОСТИ</h5>

                    @if(isset($latestNews) && $latestNews->count())
                        <ul class="news-side-list">
                            @foreach($latestNews as $n)
                                <li class="news-side-item">
                                    <a class="news-side-title" href="{{ route('news.show', $n->id) }}">
                                        {{ $n->title ?? '' }}
                                    </a>
                                    <div class="news-side-date">
                                        {{ trim(($n->description ?? '').' '.($n->description_2 ?? '')) }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div style="opacity:.7;">Пока нет новостей</div>
                    @endif
                </section>

                {{-- Похожие --}}
                @if(isset($relatedNews) && $relatedNews->count())
                    <section class="widget">
                        <h5>ПОХОЖИЕ</h5>
                        <ul class="news-side-list">
                            @foreach($relatedNews as $r)
                                <li class="news-side-item">
                                    <a class="news-side-title" href="{{ route('news.show', $r->id) }}">
                                        {{ $r->title ?? '' }}
                                    </a>
                                    <div class="news-side-date">
                                        {{ trim(($r->description ?? '').' '.($r->description_2 ?? '')) }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

            </aside>

        </div>
    </div>
</section>

{{-- Локальные стили (не ломают шаблон) --}}
<style>
.news-hero{
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    background: rgba(0,0,0,.06);
}
.news-hero img{ width:100%; display:block; }
.news-hero-fallback{
    height: 360px;
    background: linear-gradient(135deg, rgba(0,0,0,.25), rgba(0,0,0,.08));
}
.news-date-badge{
    position: absolute;
    left: 14px;
    bottom: 14px;
    background: rgba(0,0,0,.72);
    color: #fff;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: 12px;
    letter-spacing: .02em;
}
.news-header{
    margin-top: 18px;
}
.news-title{
    margin: 0;
    font-size: 28px;
    line-height: 1.25;
}
.news-subtitle{
    margin: 10px 0 0;
    font-size: 16px;
    opacity: .85;
}
.news-meta{
    display:flex;
    gap:14px;
    margin-top: 10px;
    flex-wrap: wrap;
    opacity: .75;
    font-size: 12px;
}
.news-meta-item i{ margin-right: 6px; }

.news-content{
    margin-top: 18px;
    font-size: 15px;
    line-height: 1.75;
}
.news-content p{ margin: 0 0 14px; }
.news-content img{ max-width:100%; height:auto; border-radius: 10px; }
.news-content h1,.news-content h2,.news-content h3{
    margin: 18px 0 10px;
    line-height: 1.25;
}

.news-actions{
    margin-top: 22px;
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.news-share{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap: wrap;
}
.news-share-label{
    font-size: 12px;
    opacity: .7;
}
.news-share-btn{
    border: 1px solid rgba(0,0,0,.12);
    background: #fff;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: 12px;
    color: #111;
    text-decoration: none;
    transition: all .15s ease;
}
.news-share-btn:hover{
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(0,0,0,.10);
}
.news-share-btn i{ margin-right: 6px; }

.news-nav{
    margin-top: 24px;
}
.news-nav-card{
    display:block;
    border: 1px solid rgba(0,0,0,.10);
    border-radius: 14px;
    padding: 14px 14px;
    text-decoration:none;
    color:#111;
    background: #fff;
    transition: all .15s ease;
    margin-bottom: 10px;
}
.news-nav-card:hover{
    box-shadow: 0 14px 34px rgba(0,0,0,.12);
    transform: translateY(-1px);
}
.news-nav-kicker{
    display:block;
    font-size: 12px;
    opacity: .7;
    margin-bottom: 6px;
}
.news-nav-title{
    display:block;
    font-weight: 600;
    line-height: 1.25;
}

.news-side-list{ list-style:none; padding:0; margin:0; }
.news-side-item{ padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,.06); }
.news-side-item:last-child{ border-bottom:none; }
.news-side-title{
    display:block;
    font-weight: 600;
    line-height: 1.25;
    text-decoration:none;
    color: black;
}
.news-side-date{
    font-size: 12px;
    opacity: .7;
    margin-top: 4px;
}
</style>

{{-- Копирование ссылки (микро-скрипт) --}}
<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('.news-copy');
    if(!btn) return;
    e.preventDefault();
    const url = btn.getAttribute('data-copy') || '';
    if(!url) return;

    navigator.clipboard?.writeText(url).then(function(){
        btn.innerHTML = '<i class="fa fa-check"></i> Скопировано';
        setTimeout(() => { btn.innerHTML = '<i class="fa fa-copy"></i> Скопировать'; }, 1400);
    }).catch(function(){
        // fallback
        const ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
        btn.innerHTML = '<i class="fa fa-check"></i> Скопировано';
        setTimeout(() => { btn.innerHTML = '<i class="fa fa-copy"></i> Скопировать'; }, 1400);
    });
});
</script>

@endsection
