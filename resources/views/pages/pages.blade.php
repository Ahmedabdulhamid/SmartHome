<!DOCTYPE html>
<html lang="en">
@section('title',__('web.pages'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">{{ __('web.pages') }}</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="{{ route('home') }}">{{ __('web.home') }}</a></li>
                        <li class="current">{{ __('web.pages') }}</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->
        <section id="contact" class="contact section container">
            <h1>{{ $page->getTranslation('title',app()->getLocale()) }} </h1>

            {!! $page->getTranslation('content', app()->getLocale()) !!}

        </section>

    </main>
       @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')
</body>

