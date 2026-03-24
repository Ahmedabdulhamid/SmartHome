@php
    use App\Models\Category;
    use App\Models\Currency;
    // متغير مؤقت لعدد العناصر في السلة
    $cartItemCount = 3;

    // جلب البيانات مع افتراض وجود هذه الموديلات
    // نعتمد على أن Controller يقوم بتمرير $setting و $pages
    $currencies = Currency::has('products')->get();
    $categories = Category::has('products')->get();

    // يمكنك استخدام هذه الفئة لاحقًا إذا كان قالبك يدعمها
    $isRtl = app()->getLocale() == 'ar';
@endphp

{{-- ================================================================= --}}
{{-- 1. الشريط العلوي (Top Bar) - خلفية داكنة --}}
{{-- ================================================================= --}}
<div id="topbar" class="d-none d-lg-flex align-items-center py-2 bg-dark text-white border-bottom border-secondary">
    <div class="container d-flex justify-content-between align-items-center">

        <div class="top-utils d-flex align-items-center">

            {{-- مبدل اللغة كـ Dropdown --}}
            <div class="lang-selector dropdown me-4">
                <a class="nav-link dropdown-toggle text-white fw-bold p-0" href="#" id="navbarLanguageDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $isRtl ? 'العربية' : 'English' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarLanguageDropdown">
                    @if ($isRtl)
                        <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'en']) }}">English</a>
                        </li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'ar']) }}">العربية</a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- محدد العملة كـ Dropdown --}}
            @if (isset($currencies) && count($currencies) > 0)
                <div class="currency-selector dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-bold p-0" href="#"
                        id="navbarCurrencyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ session('currency', 'EGP') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarCurrencyDropdown">
                        @foreach ($currencies as $currency)
                            <li>
                                <a class="dropdown-item @if (session('currency', 'EGP') == $currency->code) active @endif"
                                    href="/set-currency/{{ $currency->code }}">
                                    {{ $currency->code }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- الأيقونات الاجتماعية (تمت إضافة شرط تحقق) --}}
        <div class="topbar-social-links">
            @if (isset($setting))
                <a href="{{ $setting->facebook_url }}" target="_blank" class="facebook me-3 text-info">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://wa.me/{{ $setting->site_phone }}" target="_blank" class="whatsapp text-success">
                    <i class="bi bi-whatsapp"></i>
                </a>
            @endif
        </div>

    </div>
</div>

{{-- ================================================================= --}}
{{-- 2. شريط التنقل الرئيسي (Main Header) --}}
{{-- ================================================================= --}}
<header id="header" class="header d-flex align-items-center sticky-top bg-white shadow-sm">
    <div class="container position-relative d-flex align-items-center justify-content-between">

        {{-- الشعار (Logo) (تمت إضافة شرط تحقق) --}}
        <a href="{{ route('home') }}" class="logo d-flex align-items-center me-3">
            @if (isset($setting) && $setting->site_logo)
                <img src="{{ url('storage/' . $setting->site_logo) }}" alt="شعار الموقع"
                    style="max-height: 40px;">
            @else
                <span class="fs-4 fw-bold text-primary">شعار الموقع</span>
            @endif
        </a>

        {{-- حقل البحث الأنيق (للدسك توب) --}}
        <div class="search-bar d-none d-lg-block flex-grow-1 me-4" style="max-width: 400px;">
            <form class="d-flex" action="" method="GET"> {{-- Action فارغ --}}
                <div class="input-group">
                    @livewire('search_product')
                </div>
            </form>
        </div>

        {{-- قائمة التنقل الرئيسية (للدسك توب فقط) --}}
        <nav id="navmenu" class="navmenu d-none d-xl-block me-auto">
            <ul>
                {{-- الروابط الرئيسية --}}
                <li><a href="{{ route('home') }}" class="active">{{ __('web.home') }}</a></li>
                <li><a href="{{ route('about.us') }}">{{ __('web.about') }}</a></li>
                <li><a href="{{ route('rfq') }}">{{ __('web.rfq') }}</a></li>
                <li><a href="{{ route('sales.agent.index') }}">Sales AI</a></li>

                {{-- قائمة الفئات --}}
                <li class="dropdown"><a href="#"><span>{{ __('web.categories') }}</span> <i
                            class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        @if (isset($categories) && count($categories) > 0)
                            @foreach ($categories as $category)
                                <li><a
                                        href="{{ route('products.categories', $category->slug) }}">{{ $category->getTranslation('name', app()->getLocale()) }}</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>

                {{-- قائمة الصفحات (تمت إضافة شرط تحقق) --}}
                <li class="dropdown"><a href="#"><span>{{ __('web.pages') }}</span> <i
                            class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        @if (isset($pages) && count($pages) > 0)
                            @foreach ($pages as $page)
                                <li><a
                                        href="{{ route('pages', $page->slug) }}">{{ $page->getTranslation('title', app()->getLocale()) }}</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
                <li><a href="{{ route('contact.us') }}">{{ __('web.contact') }}</a></li>
                <li><a href="{{ route('faqs') }}"
                        class="{{ request()->routeIs('faqs') ? 'active' : '' }}">{{ __('web.faqs') }}</a></li>
            </ul>
        </nav>

        {{-- شريط الأدوات الرئيسي --}}
        <div class="utility-bar d-flex align-items-center ms-auto ms-xl-0 order-xl-last">

            {{-- أيقونة البحث (للموبايل والتابلت) --}}
            <a href="#" class="search-toggle text-secondary fs-4 mx-2 d-lg-none" data-bs-toggle="modal"
                data-bs-target="#searchModal">
                <i class="bi bi-search"></i>
            </a>

            {{-- أيقونة عربة التسوق (Cart Icon) --}}
            @livewire('head-nave')


            {{-- حساب المستخدم (Account Menu) --}}
            <div class="account-menu">
                <a href="#" class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-person me-1"></i> <span class="d-none d-md-inline">{{ __('web.account') }}</span>
                    <i class="bi bi-chevron-down ms-1"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    @auth
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i
                                    class="bi bi-speedometer2 me-2"></i> {{ auth()->guard('web')->user()->name }}</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i
                                        class="bi bi-box-arrow-right me-2"></i> {{ __('web.log_out') }}</button>
                            </form>
                        </li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('login') }}"><i
                                    class="bi bi-box-arrow-in-right me-2"></i> {{ __('web.login') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('register') }}"><i class="bi bi-person-plus me-2"></i>
                                {{ __('web.sign_up') }}</a></li>
                    @endauth
                </ul>
            </div>

        </div>

        {{-- زر الهمبرغر (Mobile Nav Toggle) - يستخدم Offcanvas --}}
        <button class="btn btn-link text-secondary d-xl-none p-0 ms-3" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileNavMenu" aria-controls="mobileNavMenu">
            <i class="bi bi-list fs-3"></i>
        </button>

        {{-- **ملاحظة:** تم إزالة <i class="mobile-nav-toggle ..."></i> لتجنب خطأ main.js، واستبداله بالـ <button> أعلاه. --}}

    </div>
</header>

{{-- ================================================================= --}}
{{-- 3. Modal البحث (للموبايل) --}}
{{-- ================================================================= --}}


{{-- ================================================================= --}}
{{-- 4. Mobile Menu باستخدام Offcanvas (القائمة الجانبية الحقيقية) --}}
{{-- ================================================================= --}}
<div class="offcanvas {{ $isRtl ? 'offcanvas-end' : 'offcanvas-start' }} bg-light" tabindex="-1" id="mobileNavMenu"
    aria-labelledby="mobileNavMenuLabel">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title" id="mobileNavMenuLabel">
            {{-- (تمت إضافة شرط تحقق) --}}
            @if (isset($setting))
                {{ $setting->site_title ?? 'القائمة الرئيسية' }}
            @else
                القائمة الرئيسية
            @endif
        </h5>
        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

        {{-- قائمة الروابط الرئيسية --}}
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item"><a class="nav-link active" href="{{ route('home') }}">{{ __('web.home') }}</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="{{ route('about.us') }}">{{ __('web.about') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('rfq') }}">{{ __('web.rfq') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('sales.agent.index') }}">Sales AI</a></li>
            {{-- قائمة الفئات (Dropdown) --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ __('web.categories') }}
                </a>
                <ul class="dropdown-menu border-0 shadow-sm">
                    @if (isset($categories) && count($categories) > 0)
                        @foreach ($categories as $category)
                            <li><a class="dropdown-item"
                                    href="{{ route('products.categories', $category->slug) }}">{{ $category->getTranslation('name', app()->getLocale()) }}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>

            {{-- قائمة الصفحات (Dropdown) --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ __('web.pages') }}
                </a>
                <ul class="dropdown-menu border-0 shadow-sm">
                    @if (isset($pages) && count($pages) > 0)
                        @foreach ($pages as $page)
                            <li><a class="dropdown-item"
                                    href="{{ route('pages', $page->slug) }}">{{ $page->getTranslation('title', app()->getLocale()) }}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>

            <li class="nav-item"><a class="nav-link" href="{{ route('contact.us') }}">{{ __('web.contact') }}</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="{{ route('faqs') }}">{{ __('web.faqs') }}</a></li>
        </ul>

        <hr class="my-3">

        {{-- أدوات الموبايل (العملة واللغة) --}}
        <h6 class="text-primary">{{ __('web.settings') }}</h6>
        <div class="p-2 border rounded bg-white">

            <p class="fw-bold mb-1">{{ __('web.currency') }}:</p>
            {{-- محدد العملة للموبايل (قائمة Select عادية) --}}
            @if (isset($currencies) && count($currencies) > 0)
                <form action="#" method="get">
                    <select onchange="window.location.href='/set-currency/'+this.value;" name="currency"
                        class="form-select form-select-sm mb-3">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->code }}"
                                {{ session('currency', 'EGP') == $currency->code ? 'selected' : '' }}>
                                {{ $currency->code }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <p class="fw-bold mb-1">{{ __('web.language') }}:</p>
            @if ($isRtl)
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                    class="btn btn-sm btn-outline-secondary">English</a>
            @else
                <a href="{{ route('locale.switch', ['locale' => 'ar']) }}"
                    class="btn btn-sm btn-outline-secondary">العربية</a>
            @endif

            <div class="header-social-links mt-3 d-flex justify-content-start">
                @if (isset($setting))
                    <a href="{{ $setting->facebook_url }}" target="_blank" class="facebook me-3 text-primary fs-4">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://wa.me/{{ $setting->site_phone }}" target="_blank"
                        class="whatsapp text-success fs-4">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                @endif
            </div>
        </div>

    </div>
</div>
