<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>@yield('title', 'Ti-Band')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" href="{{ asset('tiband/css/reset.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/et-line-font.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/owl.carousel.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/owl.transitions.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/lightbox.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/default.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('tiband/css/animate.css') }}" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('tiband/rs-plugin/css/settings.css') }}" media="screen" />

    <link href='https://fonts.googleapis.com/css?family=Lora:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Arimo:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="{{ asset('tiband/css/ie.css') }}">
    <script src="{{ asset('tiband/js/html5shiv.js') }}"></script>
    <script src="{{ asset('tiband/js/respond.js') }}"></script>
    <![endif]-->

    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="{{ asset('tiband/rs-plugin/css/settings-ie8.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('tiband/css/ie.css') }}">
    <![endif]-->

    <script src="{{ asset('tiband/js/jquery.js') }}"></script>

    @stack('head')
</head>

<body>
<div id="wrapper-1">

    {{-- HEADER --}}
    @include('front.partials.header')

    {{-- PAGE CONTENT --}}
    @yield('content')

    {{-- FOOTER + SIDEMENU + POPUPS + SCRIPTS --}}
    @include('front.partials.footer')

</div>

@stack('scripts')
</body>
</html>
