<!DOCTYPE html>
<html lang="en">
@section('title',__('web.contact'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">{{ __('web.contact') }}</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="{{ route('home') }}">{{ __('web.home') }}</a></li>
                        <li class="current">{{ __('web.contact') }}</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->
        <section id="contact" class="contact section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="mb-4" data-aos="fade-up" data-aos-delay="200">
                    <iframe
                        src="https://www.google.com/maps?q=27.180095542419384, 31.1921687865763&hl=ar&z=15&output=embed"
                        width="100%" height="270" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>


                </div><!-- End Google Maps -->

                <div class="row gy-4">

                    <div class="col-lg-4">
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>{{ __('web.address') }}</h3>
                                <p>{{ $setting->getTranslation('site_address',app()->getLocale()) }}</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>{{ __('web.call_us') }}</h3>
                                <p>+ {{ $setting->site_phone }}</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
                            <i class="bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>{{ __('web.email_us') }}</h3>
                                <p>{{ $setting->site_email }}</p>
                            </div>
                        </div><!-- End Info Item -->

                    </div>

                    <div class="col-lg-8">
                       @livewire('contact-us')
                    </div><!-- End Contact Form -->

                </div>

            </div>

        </section>



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
