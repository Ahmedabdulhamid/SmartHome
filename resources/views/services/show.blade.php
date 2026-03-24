<!DOCTYPE html>
<html lang="en">
@section('title', __('web.contact'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        <div class="page-title py-5 bg-light">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0 fw-bold text-dark">{{ $service->getTranslation('title', app()->getLocale()) }}
                </h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="{{ route('home') }}">{{ __('web.home') }}</a></li>
                        <li><a href="{{ route('services') }}">{{ __('web.services') }}</a></li>
                        <li class="current">{{ $service->getTranslation('title', app()->getLocale()) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <section id="service-details" class="service-details section py-5">
            <div class="container">
                <div class="row">

                    <div class="col-lg-8">

                        @if ($service->image)
                            <div class="service-image mb-4 shadow-sm rounded-4 overflow-hidden">
                                <img src="{{ url('storage/' . $service->image) }}"
                                    alt="{{ $service->getTranslation('title', app()->getLocale()) }}"
                                    class="img-fluid w-100 object-fit-cover" style="max-height: 400px;">
                            </div>
                        @endif

                        <div class="service-content mb-5 p-4 bg-white shadow-sm rounded-4">
                            <h2 class="mb-3 fw-bold text-primary">{{ __('web.service_description') }}</h2>
                            <div class="description-text lead text-secondary">
                                {{-- استخدام {!! !!} لعرض HTML الخام إذا كان الوصف غنياً بالتنسيقات --}}
                                {!! $service->getTranslation('description', app()->getLocale()) !!}
                            </div>
                        </div>

                        @if ($service->features->isNotEmpty())
                            <div
                                class="service-features mb-5 p-4 bg-light shadow-sm rounded-4 border-start border-5 border-primary">
                                <h3 class="mb-4 fw-bold text-dark"><i class="bi bi-star-fill text-warning me-2"></i>
                                    {{ __('web.key_features') }}</h3>

                                <div class="row g-3">
                                    @foreach ($service->features as $feature)
                                        <div class="col-md-6">
                                            <div
                                                class="feature-item d-flex align-items-start p-3 border rounded-3 bg-white h-100">

                                                {{-- أيقونة الميزة --}}
                                                <i
                                                    class="bi bi-check-circle-fill text-success fs-4 me-3 flex-shrink-0"></i>

                                                <div>
                                                    <h5 class="mb-1 fw-bold">
                                                        {{ $feature->getTranslation('name', app()->getLocale()) }}</h5>
                                                    <p class="text-muted small mb-0">
                                                        {{ $feature->getTranslation('description', app()->getLocale()) }}
                                                    </p>

                                                    {{-- عرض التكلفة الإضافية من جدول الربط (Pivot) --}}
                                                    @if ($feature->pivot->additional_cost > 0)
                                                        <span class="badge bg-warning text-dark mt-1">
                                                            + {{ $feature->pivot->additional_cost }}
                                                            {{ \App\Models\Currency::find($feature->pivot->currency_id)?->code ?? '' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="col-lg-4">
                        <div class="sidebar sticky-top" style="top: 20px;">

                            <div class="card shadow-lg border-0 mb-4 rounded-4">
                                <div class="card-body p-4 text-center">
                                    <h4 class="card-title fw-bold text-dark mb-3">{{ __('web.service_summary') }}</h4>

                                    <div class="price-box mb-3 p-3 rounded-3 bg-primary-subtle border border-primary">
                                        <h6 class="text-primary mb-1">{{ __('web.starting_from') }}</h6>
                                        <span class="display-6 fw-bolder text-primary">
                                            {{ $service->base_price }}
                                        </span>
                                        <span class="text-primary fs-4">
                                            {{ $service->baseCurrency?->code ?? '' }}
                                        </span>
                                    </div>

                                    <p class="mb-2">
                                        <i class="bi bi-tag-fill me-2 text-info"></i>
                                        <strong>{{ __('web.category') }}:</strong>
                                        <a href="{{--  --}}" class="text-info-emphasis fw-bold">
                                            {{ $service->category->getTranslation('name', app()->getLocale()) ?? __('web.uncategorized') }}
                                        </a>
                                    </p>

                                    @if ($service->icon)
                                        <div class="icon-summary mt-3">
                                            <img src="{{ url('storage/' . $service->icon) }}" alt="Service Icon"
                                                style="width: 60px; height: 60px; object-fit: contain;">
                                        </div>
                                    @endif

                                    <a href="{{ route('contact.us') }}"
                                        class="btn btn-danger btn-lg w-100 mt-4 shadow-sm">
                                        <i class="bi bi-envelope-fill me-2"></i> {{ __('web.request_service') }}
                                    </a>
                                </div>
                            </div>

                            @if ($service->meta_description)
                                <div class="p-3 bg-light rounded-4 small text-muted border border-secondary-subtle">
                                    <h6 class="fw-bold">{{ __('web.meta_info') }}</h6>
                                    <p class="mb-0">
                                        {{ $service->getTranslation('meta_description', app()->getLocale()) }}</p>
                                </div>
                            @endif
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
