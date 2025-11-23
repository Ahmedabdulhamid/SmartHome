<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', __('web.email_verification'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        {{-- =========================================== --}}
        {{-- ✅ قسم محتوى التحقق من البريد الإلكتروني --}}
        {{-- =========================================== --}}
        <section id="verification-prompt" class="verification-prompt pt-5">
            <div class="container" data-aos="fade-up">
                <div class="row justify-content-center">
                    <div class="col-lg-6">

                        <div class="card p-4 shadow-lg text-center">
                            <i class="bi bi-envelope-check text-primary mb-4" style="font-size: 4rem;"></i>

                            <h2 class="card-title mb-4">{{ __('web.verify_your_email') }}</h2>

                            {{-- رسالة الحالة (عندما يتم إعادة الإرسال) --}}
                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success mt-3" role="alert">
                                    {{ __('web.a_new_verification_link_has_been_sent') }}
                                </div>
                            @endif

                            <p class="text-muted mb-4">
                                {{ __('web.verification_prompt_message') }}
                            </p>

                            <div class="d-flex justify-content-center gap-3 mt-4">
                                {{-- نموذج إعادة الإرسال --}}
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('web.resend_verification_email') }}
                                    </button>
                                </form>

                                {{-- زر تسجيل الخروج --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary">
                                        {{ __('web.log_out') }}
                                    </button>
                                </form>
                            </div>

                            <hr class="mt-4">
                            <p class="small text-muted mb-0">
                                {{ __('web.no_email_received') }}
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
