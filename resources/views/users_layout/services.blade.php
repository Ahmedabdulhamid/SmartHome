<section id="services-section" class="services-section section">
    <div class="container" data-aos="fade-up">

        <div class="section-title text-center mb-5">
            <h2>{{ __('web.our_services') }}</h2>
            <p>{{ __('web.discover_our_professional_offerings') }}</p>
        </div>
         <div class="section-title d-flex align-items-center justify-content-between mb-4">
            <h1>{{ __('web.our_brands') }}</h1>
            <p><a href="{{ route('services') }}">{{ __('web.see_all_services') }}</a></p>
        </div>
        <div class="row g-4 justify-content-center">

            @if (isset($services) && count($services) > 0)
                @foreach ($services as $service)

                <div class="col-lg-4 col-md-6">

                    {{-- بطاقة الخدمة الرئيسية --}}
                    <div class="service-card h-100 position-relative overflow-hidden bg-white shadow-lg border-0 rounded-4 p-0 transition-all"
                         style="transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 1rem 3rem rgba(0,0,0,.175)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.15)';">

                        {{-- صورة الخدمة الرئيسية (خلفية علوية) --}}
                        <div class="card-image-wrapper overflow-hidden" style="height: 200px;">
                            @if ($service->image)
                                <img src="{{ url('public/storage/' . $service->image) }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $service->getTranslation('title', app()->getLocale()) }}"
                                     style="transition: transform 0.5s;"
                                     onmouseover="this.style.transform='scale(1.05)';"
                                     onmouseout="this.style.transform='scale(1)';">
                            @else
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-gear-fill text-primary fs-1"></i> {{-- أيقونة احتياطية --}}
                                </div>
                            @endif
                        </div>

                        {{-- محتوى البطاقة --}}
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- 🛑 1. مكان عرض صورة الأيقونة (الإضافة الجديدة) 🛑 --}}
                            @if ($service->icon)
                                <div class="icon-img-wrapper mb-3">
                                    <img src="{{ url('public/storage/' . $service->icon) }}"
                                         alt="{{ $service->getTranslation('title', app()->getLocale()) }} Icon"
                                         style="width: 50px; height: 50px; object-fit: contain;">
                                </div>
                            @endif

                            {{-- عنوان الخدمة --}}
                            <h3 class="card-title h5 mb-2 text-dark fw-bold">
                                {{ $service->getTranslation('title', app()->getLocale()) }}
                            </h3>

                            {{-- وصف موجز --}}
                            <p class="card-text text-secondary mb-3 flex-grow-1">
                                {!! $service->getTranslation('short_description', app()->getLocale()) !!}
                            </p>

                            {{-- سعر الخدمة الأساسي (اختياري) --}}
                            @if ($service->base_price)
                                <div class="mb-3">
                                    <span class="badge bg-success-subtle text-success fs-6 fw-bold p-2">
                                        {{ $service->base_price }}
                                        {{ $service->baseCurrency?->code ?? '' }}
                                    </span>
                                </div>
                            @endif

                            {{-- زر عرض التفاصيل --}}
                            <a href="{{-- route('services.show', $service->slug) --}}" class="btn btn-primary mt-auto stretched-link">
                                {{ __('web.details') }} <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                @endforeach
            @else
                <div class="col-12 text-center py-5">
                    <p class="lead text-muted">{{ __('web.no_services_found') }}</p>
                </div>
            @endif

        </div>
    </div>
</section>
