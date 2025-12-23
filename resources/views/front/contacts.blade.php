{{-- resources/views/front/contacts.blade.php --}}
@extends('front.layouts.app')

@section('title', 'Контакты')

@section('content')

<section class="section-bar-1 section-margin" style="padding-bottom: 20px !important;">
    <div class="container">
        <div class="callout-box-1">
            <div class="box-text">
                <h3>КОНТАКТЫ</h3>
            </div>
            <div class="box-text-2">
                <output>Главная / Контакты</output>
            </div>
        </div>
    </div>
</section>

<!-- =========== MAP ============= -->
<section class="section section-margin">
    <script src="https://maps.googleapis.com/maps/api/js?v=3.19"></script>
    <div id="google-map">
        <!-- map will be placed here -->
    </div>
</section>
<!-- ========= END ========= -->

<section class="section section-margin" style="padding-top:0px !important;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="infobox full-width center">
                    <h5>контактная информация</h5>
                    <h3>Свяжитесь с нами</h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-8 col-sm-8 col-xs-12">
                {{-- пока без логики отправки — чистая верстка --}}
                <form id="contact" class="form" method="post" action="#">
                    @csrf
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="text" name="name" placeholder="ИМЯ"/>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="email" name="email" placeholder="EMAIL"/>
                    </div>
                    <div class="col-md-12">
                        <input type="text" name="subject" placeholder="ТЕМА"/>
                    </div>
                    <div class="col-md-12">
                        <textarea name="message" placeholder="СООБЩЕНИЕ" rows="10"></textarea>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="submit" value="ГОТОВО"/>
                    </div>
                </form>

                <div id="success-message" class="center col-md-12" style="display:none;">
                    <output>Сообщение отправлено</output>
                </div>
                <div id="error-message" class="center col-md-12" style="display:none;">
                    <output>Ошибка</output>
                </div>
            </div>

            <div class="col-lg-4 col-sm-4 col-xs-12">
                <div class="infobox-2">
                    <div class="widget la-hover">
                        <h4>Информация</h4>
                        <p>Россия, г. Москва</p>
                        <p>ул. Пушкина 23а</p>
                        <p>E-Mail: mail@mail.com</p>
                        <p>Телефон: +7 123 456 78 90</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
