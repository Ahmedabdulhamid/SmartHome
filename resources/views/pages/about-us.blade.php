<!DOCTYPE html>
<html lang="en">
@section('title',__('web.about'))
@include('users_layout.head')
@section('meta_description', __('web.meta_description_smart_about_us'))
<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">{{ __('web.about') }}</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="{{ route('home') }}">{{ __('web.home') }}</a></li>
                        <li class="current">{{ __('web.about') }}</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- About Section -->
        <section id="about" class="about section">

            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                        <h2>{{ __('web.title') }}</h2>

                        <p>
                            {!! __('web.paragraph1') !!}
                        </p>

                        <p>
                            {!! __('web.paragraph2') !!}
                        </p>

                        <h3>{{ __('web.collaboration_title') }}</h3>
                        <p>
                            {!! __('web.collaboration_text') !!}
                        </p>

                        <h3>{{ __('web.impact_title') }}</h3>
                        <p>
                            {!! __('web.impact_text') !!}
                        </p>
                    </div>
                    <div class="col-lg-6 about-images" data-aos="fade-up" data-aos-delay="200">
            <div class="row gy-4">
              <div class="col-lg-6">
                <img src="assets/img/about-company-1.jpg" class="img-fluid" alt="">
              </div>
              <div class="col-lg-6">
                <div class="row gy-4">
                  <div class="col-lg-12">
                    <img src="assets/img/about-company-2.jpg" class="img-fluid" alt="">
                  </div>
                  <div class="col-lg-12">
                    <img src="assets/img/about-company-3.jpg" class="img-fluid" alt="">
                  </div>
                </div>
              </div>
            </div>

          </div>





                </div>

            </div>
        </section><!-- /About Section -->



    </main>
    @include('users_layout.footer')

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    @include('users_layout.script')

</body>

</html>
