<style>
    .brand-logo {
        height: 100px;
        /* الارتفاع اللي تحبه */
        width: auto;
        /* يحافظ على النسبة */
        object-fit: contain;
        /* يجعل الصورة تتناسب داخل المربع بدون قص */
    }
</style>
<section id="brands" class="brands section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="section-title d-flex align-items-center justify-content-between mb-4">
            <h1>{{ __('web.our_brands') }}</h1>
            <p><a href="{{ route('brands') }}">{{ __('web.see_all_brands') }}</a></p>
        </div>
        <div class="row g-5">

            @foreach ($brands as $brand)
                <div class="col-lg-3 col-md-4 col-sm-6 text-center mb-4">
                    <div class="brand-entry p-3 border rounded">
                        <a href="{{ route('products.brands', $brand->slug) }}">
                            <img src="{{ asset('storage/' . $brand->logo) }}" class="img-fluid mb-2 brand-logo">
                            <h4>{{ $brand->getTranslation('name', app()->getLocale()) }}</h4>
                        </a>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</section>
