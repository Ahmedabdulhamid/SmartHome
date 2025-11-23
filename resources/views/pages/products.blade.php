<!DOCTYPE html>
<html lang="en">
@section('title',__('web.products'))
@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-5 container">
                @if (isset($products) && count($products) > 0)
                    @foreach ($products as $product)
                        <div class="col-lg-4 d-flex">
                            <div class="post-list lg border p-3 rounded d-flex flex-column w-100">

                                {{-- صورة المنتج --}}
                                <div class="position-relative">
                                    @if ($product->firstImage)
                                        <a href="{{ route('product.details', $product->slug) }}">
                                            <img src="{{ asset('storage/' . $product->firstImage->path) }}"
                                                class="img-fluid mb-2 rounded" alt="Product Image">
                                        </a>
                                    @endif

                                    {{-- لو في خصم --}}
                                    @if ($product->has_discount)
                                        <div
                                            class="discount-badge position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded">
                                            -{{ $product->discount_percentage }}%
                                        </div>
                                    @endif
                                </div>

                                {{-- الميتا --}}
                                <div class="post-meta small text-muted">
                                    <span class="me-2">
                                        {{ $product->category->getTranslation('name', app()->getLocale()) ?? '' }}
                                    </span> |
                                    <span>
                                        {{ $product->brand->getTranslation('name', app()->getLocale()) ?? '' }}
                                    </span>
                                </div>

                                {{-- اسم المنتج --}}
                                <h2 class="h5 mt-2">
                                    <a href="{{ route('product.details', $product->slug) }}"
                                        class="text-dark text-decoration-none">
                                        {{ Str::limit($product->getTranslation('name', app()->getLocale()), 40) }}
                                    </a>
                                </h2>

                                {{-- الوصف --}}
                                <p class="flex-grow-1 text-muted">
                                    {!! Str::limit($product->getTranslation('description', app()->getLocale()), 100) !!}
                                </p>

                                {{-- السعر --}}
                                <div class="price mt-auto f">
                                    @if ($product->has_variants)
                                        <p class="text-primary">{{ __('web.has_variants') }}</p>
                                    @else
                                        @if ($product->has_discount)
                                            <div>
                                                <span class="text-danger small ms-2">

                                                    {{ number_format($product->base_price - ($product->base_price * $product->discount_percentage) / 100, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                                </span>
                                                <span class="text-muted text-decoration-line-through small ms-2 text-secondary">
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
                                <a href="{{ route('product.details', $product->slug) }}"
                                    class="btn btn-sm btn-primary mt-3 align-self-start">
                                   {{__('web.view_product')}}
                                </a>

                            </div>
                        </div>
                    @endforeach
                    {{ $products->links('vendor.pagination.bootstrap-5') }}
                @else
                    <div class="text-center my-5">
                        <img src="{{ asset('assets/img/empty-cart.webp') }}" alt="No Products" class="img-fluid"
                            style="max-width: 400px;">
                        <p class="text-muted mt-3">{{ __('No products found') }}</p>
                    </div>
                @endif

            </div>
        </div>



    </main>
    @include('users_layout.footer')

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    @include('users_layout.script')
    @livewireScripts
</body>

</html>
