<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <title>@yield('title', 'Marga')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=DM+Sans:300,400,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('marga/fonts') }}/icomoon/style.css">

  <link rel="stylesheet" href="{{ asset('marga/css') }}/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('marga/css') }}/animate.min.css">
  <link rel="stylesheet" href="{{ asset('marga/css') }}/jquery.fancybox.min.css">
  <link rel="stylesheet" href="{{ asset('marga/css') }}/owl.carousel.min.css">
  <link rel="stylesheet" href="{{ asset('marga/css') }}/owl.theme.default.min.css">
  <link rel="stylesheet" href="{{ asset('marga/fonts') }}/flaticon/font/flaticon.css">
  <link rel="stylesheet" href="{{ asset('marga/css') }}/aos.css">

  <link rel="stylesheet" href="{{ asset('marga/css') }}/style.css">

  @php
    $locale = app()->getLocale();

    $isAdmin = false;
    if (function_exists('filament')) {
        $isAdmin = filament()->auth()->check();
    } else {
        $isAdmin = auth()->check();
    }
  @endphp

  <style>
    #home-section, #about-section, #services-section, #projects-section, #testimonials-section, #blog-section, #contact-section {
      scroll-margin-top: 110px;
    }

    .projects-viewall {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      text-decoration: none;
    }
    .projects-viewall::before,
    .projects-viewall::after { content: none !important; display: none !important; }
    .projects-viewall .arrow { display: inline-flex; line-height: 1; transform: translateY(-1px); }

    .hero-cta-wrap { margin-top: 18px; }
    .hero-cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      min-width: 160px;
    }

    body.edit-mode [data-editable] {
      outline: 2px dashed rgba(255, 200, 0, 0.65);
      outline-offset: 6px;
      position: relative;
    }

    .edit-controls {
      display: none;
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 50;
      gap: 8px;
      flex-wrap: wrap;
    }
    body.edit-mode .edit-controls { display: inline-flex; }

    .edit-controls a {
      background: rgba(10, 10, 10, 0.92);
      color: #fff !important;
      font-size: 12px;
      padding: 6px 10px;
      border-radius: 10px;
      text-decoration: none !important;
      border: 1px solid rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      line-height: 1;
    }
    .edit-controls a:hover {
      transform: translateY(-1px);
      border-color: rgba(255, 200, 0, 0.35);
    }

    #edit-mode-toggle {
      position: fixed;
      top: 90px;
      right: 24px;
      z-index: 9999;
      background: rgba(10, 10, 10, 0.92);
      color: #fff;
      padding: 10px 14px;
      border-radius: 999px;
      font-size: 13px;
      cursor: pointer;
      user-select: none;
      border: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    #edit-mode-toggle .dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: rgba(255, 200, 0, 0.9);
      box-shadow: 0 0 14px rgba(255, 200, 0, 0.55);
    }
    body.edit-mode #edit-mode-toggle {
      border-color: rgba(255, 200, 0, 0.35);
      box-shadow: 0 0 22px rgba(255, 200, 0, 0.12);
    }
  </style>
</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">

@if($isAdmin)
  <div id="edit-mode-toggle">
    <span class="dot"></span>
    <span>Режим правок</span>
  </div>
@endif

<div class="site-wrap" id="home-section">

  <div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
      <div class="site-mobile-menu-close mt-3">
        <span class="icon-close2 js-menu-toggle"></span>
      </div>
    </div>
    <div class="site-mobile-menu-body"></div>
  </div>

  <header class="site-navbar site-navbar-target bg-white" role="banner" data-editable>
    @if($isAdmin)
      <div class="edit-controls">
        <a href="{{ url('/admin/content-blocks?section=layout.header&locale='.$locale) }}" target="_blank">✏️ Шапка (тексты)</a>
      </div>
    @endif

    <div class="container">
      <div class="row align-items-center position-relative">

        <div class="col-lg-4">
          <nav class="site-navigation text-right ml-auto" role="navigation">
            <ul class="site-menu main-menu js-clone-nav ml-auto d-none d-lg-block">

              <li class="active"><a href="#home-section" class="nav-link">{!! $layoutHeader['menu_left_home'] ?? 'Home' !!}</a></li>

              @if(($sectionActive['home-projects'] ?? false))
                <li><a href="#projects-section" class="nav-link">{!! $layoutHeader['menu_left_projects'] ?? 'Projects' !!}</a></li>
              @endif

              @if(($sectionActive['home-services'] ?? false))
                <li><a href="#services-section" class="nav-link">{!! $layoutHeader['menu_left_services'] ?? 'Services' !!}</a></li>
              @endif

            </ul>
          </nav>
        </div>

        <div class="col-lg-4 text-center">
          <div class="site-logo">
            <a href="{{ url('/') }}">
              {!! $layoutHeader['logo_text'] ?? 'Marga' !!}
            </a>
          </div>

          <div class="ml-auto toggle-button d-inline-block d-lg-none">
            <a href="#" class="site-menu-toggle py-5 js-menu-toggle text-white">
              <span class="icon-menu h3 text-primary"></span>
            </a>
          </div>
        </div>

        <div class="col-lg-4">
          <nav class="site-navigation text-left mr-auto" role="navigation">
            <ul class="site-menu main-menu js-clone-nav ml-auto d-none d-lg-block">

              @if(($sectionActive['home-about-slider'] ?? false))
                <li><a href="#about-section" class="nav-link">{!! $layoutHeader['menu_right_about'] ?? 'About' !!}</a></li>
              @endif

              @if(($sectionActive['home-blog'] ?? false))
                <li><a href="#blog-section" class="nav-link">{!! $layoutHeader['menu_right_blog'] ?? 'Blog' !!}</a></li>
              @endif

              @if(($sectionActive['layout-footer'] ?? true))
                <li><a href="#contact-section" class="nav-link">{!! $layoutHeader['menu_right_contact'] ?? 'Contact' !!}</a></li>
              @endif

              <li class="has-children">
                <a href="#" class="nav-link">{{ strtoupper($locale) }}</a>
                <ul class="dropdown">
                  <li><a href="{{ route('set-locale', 'en') }}">EN</a></li>
                  <li><a href="{{ route('set-locale', 'ru') }}">RU</a></li>
                  <li><a href="{{ route('set-locale', 'ro') }}">RO</a></li>
                </ul>
              </li>

            </ul>
          </nav>
        </div>

      </div>
    </div>
  </header>

  {{-- ✅ HERO / BANNERS (целиком скрывается если секция выключена) --}}
  @if(($sectionActive['home-hero'] ?? false) && $banners->count())
    <div class="owl-carousel-wrapper" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/banners') }}" target="_blank">🖼 Баннеры</a>
          <a href="{{ url('/admin/content-blocks?section=layout.header&locale='.$locale) }}" target="_blank">✏️ Кнопка/шапка</a>
        </div>
      @endif

      <div class="box-92819">
        <div class="owl-carousel slide-one-item-alt-text">
          @foreach($banners as $banner)
            <div class="d-flex align-items-center" style="min-height: 400px;">
              <div>
                <h1 class="text-uppercase mb-3">{{ $banner->title }}</h1>

                @if($banner->text)
                  <p class="mb-5">{!! nl2br(e($banner->text)) !!}</p>
                @endif
              </div>
            </div>
          @endforeach
        </div>

        <div class="hero-cta-wrap">
          <a href="#contact-section" class="btn btn-primary rounded-0 hero-cta">
            {!! $layoutHeader['hero_button_contact'] ?? 'Contact Us' !!}
          </a>
        </div>
      </div>

      <div class="owl-carousel owl-1">
        @foreach($banners as $banner)
          <div class="ftco-cover-1" style="background-image: url('{{ asset('storage/' . $banner->image_path) }}');"></div>
        @endforeach
      </div>
    </div>
  @endif


  {{-- ✅ ABOUT + SLIDER (целиком скрывается если секция выключена) --}}
  @if(($sectionActive['home-about-slider'] ?? false))
    <div class="site-section" id="about-section" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/content-blocks?section=home.about_card&locale='.$locale) }}" target="_blank">✏️ About (тексты)</a>
          <a href="{{ url('/admin/sliders') }}" target="_blank">🖼 About (слайдер)</a>
        </div>
      @endif

      <div class="container">
        <div class="row align-items-stretch">
          <div class="col-lg-4">
            <div class="h-100 bg-white box-29291">
              <h2 class="heading-39291">
                {!! $aboutCard['home.about_card.title'] ?? 'Welcome To <br> Our Company' !!}
              </h2>

              {!! $aboutCard['home.about_card.text_1'] ?? '<p>Lorem ipsum dolor sit amet...</p>' !!}
              {!! $aboutCard['home.about_card.text_2'] ?? '<p>Alias odit ipsam quas...</p>' !!}

              <p class="mt-5">
                <span class="d-block font-weight-bold text-black">
                  {!! $aboutCard['home.about_card.name'] ?? 'Bruce Smith' !!}
                </span>
                <span class="d-block font-weight-bold text-muted">
                  {!! $aboutCard['home.about_card.role'] ?? 'Founder, CEO' !!}
                </span>

                <img src="{{ asset('marga/images') }}/signature.svg" alt="Image" class="img-fluid" width="140">
              </p>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="owl-carousel owl-3">
              @forelse($sliders as $slide)
                @if($slide->image_path)
                  <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->id }}" class="img-fluid">
                @endif
              @empty
                <img src="{{ asset('marga/images') }}/about_1.jpg" alt="Image" class="img-fluid">
                <img src="{{ asset('marga/images') }}/about_2.jpg" alt="Image" class="img-fluid">
                <img src="{{ asset('marga/images') }}/about_3.jpg" alt="Image" class="img-fluid">
              @endforelse
            </div>
          </div>

        </div>
      </div>
    </div>
  @endif


  {{-- ✅ WHAT WE DO / SERVICES --}}
  @if(($sectionActive['home-services'] ?? false) && $cards->count())
    <div class="site-section" id="services-section" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/cards') }}" target="_blank">🧩 Карточки</a>
          <a href="{{ url('/admin/content-blocks?section=home.sections&locale='.$locale) }}" target="_blank">✏️ Заголовки секций</a>
        </div>
      @endif

      <div class="container">
        <div class="row mb-5 align-items-center">
          <div class="col-md-7">
            <h2 class="heading-39291 mb-0">{!! $homeSections['what_we_do_title'] ?? 'What We Do' !!}</h2>
          </div>
        </div>

        <div class="row">
          @foreach($cards as $index => $card)
            <div class="col-md-6 mb-4 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
              <div class="service-29193 text-center" data-editable>
                @if($isAdmin)
                  <div class="edit-controls">
                    <a href="{{ url('/admin/cards/'.$card->id.'/edit') }}" target="_blank">✏️ Карточка</a>
                  </div>
                @endif

                <span class="img-wrap mb-5">
                  @if($card->image_path)
                    <img src="{{ asset('storage/' . $card->image_path) }}" alt="{{ $card->title }}" class="img-fluid">
                  @else
                    <img src="{{ asset('marga/fonts/flaticon/svg/001-stairs.svg') }}" alt="{{ $card->title }}" class="img-fluid">
                  @endif
                </span>

                <h3 class="mb-4"><a href="#">{{ $card->title }}</a></h3>
                @if($card->description) <p>{{ $card->description }}</p> @endif
              </div>
            </div>
          @endforeach
        </div>

      </div>
    </div>
  @endif


  {{-- ✅ OUR PROJECTS --}}
  @if(($sectionActive['home-projects'] ?? false) && $projects->count())
    <div class="site-section" id="projects-section" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/projects') }}" target="_blank">📁 Проекты</a>
          <a href="{{ url('/admin/content-blocks?section=home.sections&locale='.$locale) }}" target="_blank">✏️ Заголовки секций</a>
        </div>
      @endif

      <div class="container">
        <div class="row mb-5 align-items-center">
          <div class="col-md-7">
            <h2 class="heading-39291 mb-0">{!! $homeSections['our_projects_title'] ?? 'Our Projects' !!}</h2>
          </div>
          <div class="col-md-5 text-right">
            <p class="mb-0">
              <a href="#projects-section" class="projects-viewall">
                <span class="more-39291__text">{!! $homeSections['our_projects_view_all'] ?? 'View All Projects' !!}</span>
                <span class="arrow" aria-hidden="true">→</span>
              </a>
            </p>
          </div>
        </div>

        <div class="row">
          @foreach($projects as $project)
            <div class="col-lg-6">
              <div class="media-02819" data-editable>
                @if($isAdmin)
                  <div class="edit-controls">
                    <a href="{{ url('/admin/projects/'.$project->id.'/edit') }}" target="_blank">✏️ Проект</a>
                  </div>
                @endif

                @if($project->image_path)
                  <a href="#" class="img-link {{ $loop->iteration === 1 || $loop->iteration === 4 ? 'small' : '' }}">
                    <img src="{{ asset('storage/' . $project->image_path) }}" alt="{{ $project->title }}" class="img-fluid">
                  </a>
                @endif

                <h3><a href="#">{{ $project->title }}</a></h3>
                @if($project->description)
                  <span>{!! nl2br(e($project->description)) !!}</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>

      </div>
    </div>
  @endif


  {{-- ✅ QUOTES / TESTIMONIALS --}}
  @if(($sectionActive['home-testimonials'] ?? false) && $quotes->count())
    <div class="site-section section-4" id="testimonials-section" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/quotes') }}" target="_blank">💬 Отзывы</a>
        </div>
      @endif

      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-md-7">
            <div class="slide-one-item owl-carousel">
              @foreach($quotes as $quote)
                <blockquote class="testimonial-1" data-editable>
                  @if($isAdmin)
                    <div class="edit-controls">
                      <a href="{{ url('/admin/quotes/'.$quote->id.'/edit') }}" target="_blank">✏️ Отзыв</a>
                    </div>
                  @endif

                  <span class="quote quote-icon-wrap">
                    <span class="icon-format_quote"></span>
                  </span>

                  <p>{!! nl2br(e($quote->text)) !!}</p>

                  <cite>
                    <span class="text-black">{{ $quote->author }}</span>
                    &mdash;
                    <span class="text-muted">{{ $quote->role }}</span>
                  </cite>
                </blockquote>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif


  {{-- ✅ BLOG --}}
  @php
    $blogTitle = $blogIntro['title'] ?? '';
    $blogText  = $blogIntro['text'] ?? '';
  @endphp

  @if(($sectionActive['home-blog'] ?? false))
    <div class="site-section" id="blog-section" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/content-blocks?section=home.blog_intro&locale='.$locale) }}" target="_blank">✏️ Blog intro</a>
          <a href="{{ url('/admin/blog-cards') }}" target="_blank">📰 Blog cards</a>
        </div>
      @endif

      <div class="container">
        <div class="row mb-5">
          <div class="col-md-7">
            @if($blogTitle) <h2 class="heading-39291">{!! $blogTitle !!}</h2> @endif
            @if($blogText) {!! $blogText !!} @endif
          </div>
        </div>

        <div class="row align-items-stretch">
          @foreach($blogCards as $post)
            <div class="col-lg-3 col-md-6 mb-5">
              <div class="post-entry-1 h-100" data-editable>
                @if($isAdmin)
                  <div class="edit-controls">
                    <a href="{{ url('/admin/blog-cards/'.$post->id.'/edit') }}" target="_blank">✏️ Пост</a>
                  </div>
                @endif

                <div class="post-entry-1-contents">
                  @if($post->date)
                    <span class="meta">{{ $post->date->format('F d, Y') }}</span>
                  @endif

                  <h2><a href="{{ $post->url ?: '#' }}">{{ $post->title }}</a></h2>

                  @if($post->url)
                    <p class="my-3">
                      <a href="{{ $post->url }}" class="more-39291">
                        {!! $blogIntro['read_more_label'] ?? 'Read More' !!}
                      </a>
                    </p>
                  @endif
                </div>

              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif


  {{-- ✅ FOOTER --}}
  @if(($sectionActive['layout-footer'] ?? true))
    <footer class="site-footer" id="contact-section" data-editable>
      @if($isAdmin)
        <div class="edit-controls">
          <a href="{{ url('/admin/content-blocks?section=layout.footer&locale='.$locale) }}" target="_blank">✏️ Footer (тексты)</a>
        </div>
      @endif

      <div class="container">
        <div class="row">

          <div class="col-md-6">
            <div class="row">
              <div class="col-md-7">
                <h2 class="footer-heading mb-4">{!! $footerBlocks['about_title'] ?? 'About Us' !!}</h2>
                <p>{!! $footerBlocks['about_text'] ?? '...' !!}</p>
              </div>

              <div class="col-md-4 ml-auto">
                <h2 class="footer-heading mb-4">{!! $footerBlocks['features_title'] ?? 'Features' !!}</h2>
                <ul class="list-unstyled">
                  <li><a href="#about-section">{!! $footerBlocks['features_link_about'] ?? 'About Us' !!}</a></li>
                  <li><a href="#testimonials-section">{!! $footerBlocks['features_link_testimonials'] ?? 'Testimonials' !!}</a></li>
                  <li><span>{!! $footerBlocks['features_link_terms'] ?? 'Terms of Service' !!}</span></li>
                  <li><span>{!! $footerBlocks['features_link_privacy'] ?? 'Privacy' !!}</span></li>
                  <li><a href="#contact-section">{!! $footerBlocks['features_link_contact'] ?? 'Contact Us' !!}</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-4 ml-auto">
            <div class="mb-5">
              <h2 class="footer-heading mb-4">{!! $footerBlocks['newsletter_title'] ?? 'Subscribe to Newsletter' !!}</h2>

              <form action="#" method="post" class="footer-suscribe-form">
                <div class="input-group mb-3">
                  <input type="text" class="form-control rounded-0 border-secondary text-white bg-transparent"
                         placeholder="{!! $footerBlocks['newsletter_placeholder'] ?? 'Enter Email' !!}">
                  <div class="input-group-append">
                    <button class="btn btn-primary text-white" type="button">
                      {!! $footerBlocks['newsletter_button'] ?? 'Subscribe' !!}
                    </button>
                  </div>
                </div>

                <h2 class="footer-heading mb-4">{!! $footerBlocks['follow_us_title'] ?? 'Follow Us' !!}</h2>

                <a href="{{ $footerBlocks['social_facebook_url'] ?? '#' }}" class="smoothscroll pl-0 pr-3"><span class="icon-facebook"></span></a>
                <a href="{{ $footerBlocks['social_twitter_url'] ?? '#' }}" class="pl-3 pr-3"><span class="icon-twitter"></span></a>
                <a href="{{ $footerBlocks['social_instagram_url'] ?? '#' }}" class="pl-3 pr-3"><span class="icon-instagram"></span></a>
                <a href="{{ $footerBlocks['social_linkedin_url'] ?? '#' }}" class="pl-3 pr-3"><span class="icon-linkedin"></span></a>
              </form>
            </div>
          </div>

        </div>

        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <div class="pt-5">
              <p class="small">
                {!! $footerBlocks['copyright_text']
                  ?? 'Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved' !!}
              </p>
            </div>
          </div>
        </div>

      </div>
    </footer>
  @endif

</div>

<script src="{{ asset('marga/js') }}/jquery-3.3.1.min.js"></script>
<script src="{{ asset('marga/js') }}/popper.min.js"></script>
<script src="{{ asset('marga/js') }}/bootstrap.min.js"></script>
<script src="{{ asset('marga/js') }}/owl.carousel.min.js"></script>
<script src="{{ asset('marga/js') }}/jquery.sticky.js"></script>
<script src="{{ asset('marga/js') }}/jquery.waypoints.min.js"></script>
<script src="{{ asset('marga/js') }}/jquery.animateNumber.min.js"></script>
<script src="{{ asset('marga/js') }}/jquery.fancybox.min.js"></script>
<script src="{{ asset('marga/js') }}/jquery.easing.1.3.js"></script>
<script src="{{ asset('marga/js') }}/aos.js"></script>
<script src="{{ asset('marga/js') }}/main.js"></script>

@if($isAdmin)
<script>
  (function () {
    const key = 'front_edit_mode';
    let editMode = localStorage.getItem(key) === '1';

    const apply = () => document.body.classList.toggle('edit-mode', editMode);
    apply();

    const btn = document.getElementById('edit-mode-toggle');
    if (!btn) return;

    btn.addEventListener('click', () => {
      editMode = !editMode;
      localStorage.setItem(key, editMode ? '1' : '0');
      apply();
    });
  })();
</script>
@endif

</body>
</html>
