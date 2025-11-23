<section id="categories" class="categories section">
    <div class="container" data-aos="fade-up">

        <!-- Section Title -->
        <div class="section-title d-flex align-items-center justify-content-between mb-4">
            <h2>{{ __('web.categories') }}</h2>
            <p><a href="{{ route('categories') }}">{{ __('web.see_all_categories') }}</a></p>
        </div>

        <div class="row g-4">

            @foreach ($categories as $category)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="category-entry p-3 border rounded text-center">
                        <a href="{{ route('products.categories', $category->slug) }}" class="d-block mb-4">
                            <h4>{{ $category->getTranslation('name', app()->getLocale()) }}</h4>

                        </a>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>
