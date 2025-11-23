<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', __('web.faq_title'))
@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <div class="container py-5" data-aos="fade-up" data-aos-delay="100">

            <section class="text-center mb-5 pt-4">
                <h1 class="display-4 fw-bold text-primary">{{ __('web.faq_heading') }}</h1>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    {{ __('web.faq_subheading') }}
                </p>
            </section>

            <hr class="my-5 border-2 w-75 mx-auto">

            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="accordion" id="faqAccordion">

                        {{-- حلقة Blade لعرض بيانات Faq Model --}}
                        @foreach($faqs as $index => $faq)
                            @php
                                $collapseId = 'collapse' . $faq->id;
                                $headingId = 'heading' . $faq->id;
                                $isFirst = $index === 0;
                            @endphp

                            <div class="accordion-item shadow-sm mb-3" data-aos="fade-up" data-aos-delay="{{ 50 * ($index + 1) }}">
                                <h2 class="accordion-header" id="{{ $headingId }}">
                                    <button class="accordion-button @if (!$isFirst) collapsed @endif fw-bold"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $collapseId }}"
                                            aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                            aria-controls="{{ $collapseId }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>

                                <div id="{{ $collapseId }}"
                                     class="accordion-collapse collapse @if ($isFirst) show @endif"
                                     aria-labelledby="{{ $headingId }}"
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        <p>{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <section class="text-center mt-5 p-4 bg-light rounded-3 shadow-sm">
                <h3 class="fw-bold">{{ __('web.faq_not_found_title') }}</h3>
                <p>{{ __('web.faq_not_found_text') }}</p>
                <a href="{{ route('contact.us') }}" class="btn btn-primary btn-lg mt-2">
                    {{ __('web.contact_us_now') }} <i class="bi bi-chat-dots"></i>
                </a>
            </section>

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
