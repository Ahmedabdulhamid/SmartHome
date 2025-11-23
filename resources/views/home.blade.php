<!DOCTYPE html>
<html lang="en">
@section('title', __('web.home'))
@include('users_layout.head')
@section('meta_description', __('web.meta_description_smart_home'))

    <body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
        @include('users_layout.header')

        <main class="main">

            <!-- Slider Section -->
            <section id="slider" class="slider section dark-background">

                <div class="container" data-aos="fade-up" data-aos-delay="100">

                    <div class="swiper init-swiper">

                        @include('users_layout.script_aplication')

                        @include('users_layout.swiper')
                    </div>

                </div>

            </section><!-- /Slider Section -->
            <!-- Services Section -->
            @include('users_layout.services')
            <!-- /Services Section -->
            <!-- Blogs -->
            @include('users_layout.blogs')
            <!-- /Blogs -->

            <!-- brands -->
            @include('users_layout.brands')<!-- /brands -->

            <!-- Culture Category Section -->
            @include('users_layout.categories')<!-- /Culture Category Section -->

            <!-- Downloads -->
            @include('users_layout.downloads')
            <!-- /Downloads -->

            <!-- Products -->
            @include('users_layout.products')
            <!-- /Products -->

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
