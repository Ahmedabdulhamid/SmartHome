<!DOCTYPE html>
<html lang="en">
@section('title',__('web.categories'))
@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
    @else
    direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            {{--
                **تم التعديل هنا:** أضفنا gx-0 لإزالة المسافة الأفقية (gutter) على الهواتف
                لحل مشكلة التمرير الأفقي، مع الاحتفاظ بـ g-5 لشاشات SM فما فوق.
            --}}
            <div class="row g-5 my-5 **gx-0 gx-sm-5** container">
                @if (isset($categories) && count($categories) > 0)

                    @foreach ($categories as $category)
                        {{-- يمكن أيضاً تقليل حجم العمود إلى col-lg-4 col-md-6 col-sm-6 لترتيب أفضل على الأجهزة اللوحية --}}
                        <div class="col-lg-3 col-md-4 col-sm-6 my-5">
                            <div class="category-entry p-3 border rounded text-center">
                                <a href="{{ route('products.categories', $category->slug) }}" class="d-block mb-4">
                                    <h4>{{ $category->getTranslation('name', app()->getLocale()) }}</h4>

                                </a>
                            </div>
                        </div>
                    @endforeach
                    {{ $categories->links('vendor.pagination.bootstrap-5') }}
                @else
                    <div class="text-center my-5 container">
                        <img src="{{ asset('assets/img/empty-cart.webp') }}" alt="No Products" class="img-fluid container"
                            style="max-width: 300px;">
                        <p class="text-muted mt-3">{{ __('No products found') }}</p>
                    </div>
                @endif

            </div>
        </div>
    </main>

    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')
    @livewireScripts
</body>

</html>
