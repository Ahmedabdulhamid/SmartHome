<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', __('web.forgot_password'))
@include('users_layout.head')

<style>
    /* Custom Styles for a beautiful Forgot Password Form */
    .verification-prompt {
        min-height: 100vh;
        display: flex;
        align-items: center;
        /* خلفية متدرجة أنيقة */
        background: linear-gradient(135deg, #f0f4f8 0%, #e0e7f0 100%);
    }

    .card {
        border: none;
        border-radius: 1rem;
        /* ظل بارز للبطاقة */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        /* تأثير رفع طفيف عند المرور بالماوس */
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        color: #0d6efd; /* اللون الأزرق الأساسي للعنوان */
        font-weight: 700;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        font-weight: 600;
        padding: 0.6rem 2rem;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
        transform: translateY(-1px);
    }

    .form-control {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d9e6;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        {{-- =========================================== --}}
        {{-- ✅ قسم محتوى طلب إعادة تعيين كلمة المرور --}}
        {{-- =========================================== --}}
        <section id="forgot-password-prompt" class="verification-prompt py-5">
            <div class="container" data-aos="fade-up">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">

                        <div class="card p-5 shadow-lg text-center">
                            <i class="bi bi-envelope-exclamation text-primary mb-4" style="font-size: 4rem;"></i>

                            <h2 class="card-title mb-4">{{ __('web.forgot_your_password') }}</h2>

                            {{-- رسالة الحالة (عندما يتم إعادة الإرسال بنجاح) --}}
                            @if (session('status'))
                                <div class="alert alert-success mt-3" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <p class="text-muted mb-4">
                                {{ __('web.password_reset_message') }}
                            </p>

                            <form method="POST" action="{{ route('password.email') }}" class="text-start">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label">{{ __('web.your_email') }}</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com" required autofocus>
                                    @error('email')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('web.send_reset_link') }}
                                    </button>
                                </div>
                            </form>

                            <hr class="mt-4">
                            <p class="small text-muted mb-0">
                                <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                                    &larr; {{ __('web.back_to_login') }}
                                </a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')

</body>

</html>
