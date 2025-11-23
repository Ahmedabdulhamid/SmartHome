<style>
    .grecaptcha-badge {
            visibility: visible !important;
            opacity: 1 !important;
            bottom: 20px !important;
            z-index:100000 !important;
        }
</style>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

{{-- 🚀 العنوان المُحسن للـ SEO - تم تضمين "Smart Home" كقيمة افتراضية --}}
<title>@yield('title', 'RY | حلول وأنظمة المنزل الذكي')</title>

{{-- 🚀 الوصف التعريفي المُحسن - تم تضمين وصف جذاب لخدماتك --}}
<meta name="description" content="@yield('meta_description', 'أفضل خدمات وتركيبات Smart Home في مصر. تحكم كامل في منزلك.')">

<meta name="keywords" content="@yield('meta_keywords', 'Smart Home, منزل ذكي, أتمتة المنازل, RY')">

<base href="{{ url('/') }}/">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="{{ Storage::url($setting->site_logo) }}" rel="icon">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

<link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

<link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

@stack('styles')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

 <script src="https://www.google.com/recaptcha/api.js"></script>
