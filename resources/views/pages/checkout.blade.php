<!DOCTYPE html>
<html lang="en">
@section('title', __('web.checkout'))
@include('users_layout.head')

{{-- إضافة الروابط للتصميم الجديد --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>


    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7ff;
    }

    .checkout-page h1 {
        font-size: 2.5rem;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
    }

    .card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 16px;
        border: none;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12) !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .btn-primary {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border: none;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, var(--primary-dark), #ff5577);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(74, 111, 255, 0.3);
    }

    @media (max-width: 768px) {
        .checkout-page .row {
            flex-direction: column-reverse;
        }

        .checkout-page h1 {
            font-size: 2rem;
        }
    }
</style>

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main container">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
          <div class="row g-5 my-5 gx-0 gx-sm-5 container">
              @livewire('checkout-page')
            </div>
        </div>
    </main>

    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <div id="preloader"></div>

    @include('users_layout.script')
    @livewireScripts
</body>
</html>
