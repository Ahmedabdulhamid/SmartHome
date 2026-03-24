<!DOCTYPE html>
<html lang="en">
@section('title', __('web.carts'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
 @else
 direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
          <div class="row g-5 my-5 **gx-0 gx-sm-5** container">
            @livewire('cart-page')

            </div>
        </div>



    </main>
    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')
    @livewireScripts
</body>

</html>
