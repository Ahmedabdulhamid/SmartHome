<section id="lifestyle-category" class="lifestyle-category section">

    <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
            <h2>{{ __('web.products') }}</h2>
            <p><a href="{{ route('products') }}">{{ __('web.see_all_products') }}</a></p>
        </div>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-5">
            @foreach ($products as $product)
                <div class="col-lg-4 d-flex">
                    <div class="post-list lg border p-3 rounded d-flex flex-column w-100">

                        {{-- صورة المنتج --}}
                        <div class="position-relative">
                            @if ($product->firstImage)
                                <a href="{{route('product.details',$product->slug)}}">
                                    <img src="{{ asset('storage/' . $product->firstImage->path) }}"
                                         class="img-fluid mb-2 rounded"
                                         alt="Product Image">
                                </a>
                            @endif

                            {{-- لو في خصم --}}
                            @if ($product->has_discount)
                                <div class="discount-badge position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded">
                                    -{{ $product->discount_percentage }}%
                                </div>
                            @endif
                        </div>

                        {{-- الميتا --}}
                        <div class="post-meta small text-muted">
                            <span class="me-2">
                                {{ $product?->category?->getTranslation('name', app()->getLocale()) ?? '' }}
                            </span> |
                            <span>
                                {{ $product?->brand?->getTranslation('name', app()->getLocale()) ?? '' }}
                            </span>
                        </div>

                        {{-- اسم المنتج --}}
                        <h4 class="h5 mt-2">
                            <a href="{{route('product.details',$product->slug)}}" class="text-dark text-decoration-none">
                                {{ Str::limit($product->getTranslation('name', app()->getLocale()), 40) }}
                            </a>
                        </h4>

                        {{-- الوصف --}}
                        <p class="flex-grow-1 text-muted">
                            {!! Str::limit($product->getTranslation('description', app()->getLocale()), 100) !!}
                        </p>

                        {{-- السعر --}}
                        <div class="price mt-auto ">
                            @if ($product->has_variants)
                                <p class="text-primary">{{ __('web.has_variants') }}</p>
                            @else
                                @if ($product->has_discount)
                                    <div>
                                        <span class="text-danger">
                                            {{ number_format($product->base_price - ($product->base_price * $product->discount_percentage) / 100, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                        </span>
                                        <span class="text-muted text-decoration-line-through small ms-2">
                                            {{ number_format($product->base_price, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                        </span>
                                    </div>
                                @else
                                    <div>
                                        {{ number_format($product->base_price, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- زرار التفاصيل --}}
                        <a href="{{route('product.details',$product->slug)}}"
                           class="btn btn-sm btn-primary text-light mt-3 align-self-start">
                            {{ __('web.view_product') }}
                        </a>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

</section>

<style>
    .discount-badge {
        font-size: 0.85rem;
        font-weight: bold;
        z-index: 5;
    }
</style>
