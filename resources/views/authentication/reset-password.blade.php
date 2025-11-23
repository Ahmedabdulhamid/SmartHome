<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', __('web.reset_password_title'))
@include('users_layout.head')

<style>
/* أنماط مخصصة لصفحة إعادة تعيين كلمة المرور (Reset Password) */
#reset-password-form {
    /* خلفية متدرجة ناعمة لكامل القسم */
    background: #f8f9fa; /* لون احتياطي */
    background: linear-gradient(135deg, #f0f4f7 0%, #e9ecef 100%);
    padding-top: 80px !important; /* ضمان التباعد عن الرأس */
    padding-bottom: 80px !important;
}

.card {
    border: none;
    border-radius: 15px; /* حواف دائرية */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); /* ظل أقوى وأكثر بروزاً */
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-3px); /* رفع طفيف عند التمرير */
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

.card-title {
    color: #007bff; /* استخدام اللون الأساسي للعنوان */
    font-weight: 700;
}

/* تنسيق الأيقونة */
.bi-key {
    color: #007bff !important; /* ضمان اللون الأساسي للأيقونة */
}

/* تنسيق الزر الأساسي */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    font-weight: 600;
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
    transform: translateY(-1px); /* تأثير ضغط خفيف */
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
}

/* تنسيق التركيز على حقل الإدخال */
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* تنسيق رابط العودة إلى تسجيل الدخول */
.text-muted a {
    color: #6c757d !important;
    text-decoration: none;
    transition: color 0.3s;
}

.text-muted a:hover {
    color: #007bff !important;
    text-decoration: underline;
}
</style>

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        {{-- =========================================== --}}
        {{-- ✅ قسم محتوى إعادة تعيين كلمة المرور --}}
        {{-- =========================================== --}}
        <section id="reset-password-form" class="reset-password-form pt-5 d-flex align-items-center" style="min-height: 80vh;">
            <div class="container" data-aos="fade-up">
                <div class="row justify-content-center">
                    <div class="col-lg-6">

                        <div class="card p-4 shadow-lg">
                            <i class="bi bi-key text-primary mb-4 text-center" style="font-size: 4rem;"></i>

                            <h2 class="card-title mb-4 text-center">{{ __('web.reset_password_title') }}</h2>

                            {{-- رسائل الحالة والأخطاء --}}
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {{-- رسالة توضيحية --}}
                            <p class="text-muted mb-4 text-center">
                                {{ __('web.reset_password_message') }}
                            </p>

                            {{-- نموذج إعادة تعيين كلمة المرور --}}
                            <form method="POST" action="{{ route('password.store') }}" class="">
                                @csrf

                                {{-- حقل الرمز السري (يجب أن يكون مخفياً ويأتي من الرابط) --}}
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                {{-- حقل البريد الإلكتروني --}}
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" placeholder="{{ __('web.your_email') }}"
                                           value="{{ old('email', $request->email) }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل كلمة المرور الجديدة --}}
                                <div class="mb-3">
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" placeholder="{{ __('web.new_password') }}"
                                           required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل تأكيد كلمة المرور --}}
                                <div class="mb-3">
                                    <input type="password" name="password_confirmation" class="form-control"
                                           id="password-confirm" placeholder="{{ __('web.confirm_password') }}"
                                           required autocomplete="new-password">
                                </div>

                                {{-- زر الإرسال --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        {{ __('web.reset_password_btn') }}
                                    </button>
                                </div>

                                <hr class="my-4">

                                {{-- رابط العودة إلى تسجيل الدخول --}}
                                <div class="text-center">
                                    <a href="{{ route('login') }}" class="text-muted small">
                                        {{ __('web.back_to_login') }}
                                    </a>
                                </div>

                            </form>

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
