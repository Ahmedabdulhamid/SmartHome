<div x-data="{
    activeImage: '{{ count($product->images) ? url('storage/' . $product->images->first()->path) : '' }}',
    activeVariant: null,

    // دالة لدمج جميع صور المنتج الأساسية وصور الـ Variants في قائمة واحدة
    getAllImages: function() {
        let images = [
            @foreach ($product->images as $image)
                '{{ url('storage/' . $image->path) }}', @endforeach
        ];
        @foreach ($product->variants as $variant)
            @foreach ($variant->variantImages as $vimg)
                images.push('{{ url('storage/' . $vimg->path) }}'); @endforeach
        @endforeach
        return [...new Set(images)];
    },

    // دالة لتحديث الصورة النشطة والـ Variant النشط
    setActiveVariantAndImage: function(variantData) {
        this.activeVariant = variantData;

        if (variantData.images.length) {
            this.activeImage = variantData.images[0];
        } else {
            this.activeImage = '{{ count($product->images) ? url('storage/' . $product->images->first()->path) : '' }}';
        }
    }

}" class="container py-4 py-lg-5">
    <div class="row g-4 container">

        {{-- منطقة الصور (العمود الأيسر) --}}
        <div class="col-lg-6 col-md-12">
            <div class="d-flex flex-column">

                {{-- الصورة الرئيسية الكبيرة --}}
                <div class="rounded border p-3 bg-white text-center shadow-sm mb-3">
                    <img :src="activeImage" class="img-fluid rounded"
                        style="max-height: 550px; object-fit: contain; width: 100%;" alt="Main Product Image">
                </div>

                {{-- الصور المصغرة (Thumbnails): تظهر عمودياً على الديسكتوب وأفقياً على الموبايل --}}
                <div class="d-flex gap-2 p-1 overflow-auto image-thumbnails-desktop">
                    <template x-for="(img, index) in getAllImages()" :key="index">
                        <img :src="img" alt="Thumbnail" class="rounded border p-1 flex-shrink-0"
                            :class="activeImage === img ? 'border-info border-2 shadow' : 'border-light opacity-75'"
                            style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                            @click="activeImage = img">
                    </template>
                </div>
            </div>
        </div>

        {{-- تفاصيل المنتج والسعر واختيار الـ Variants (العمود الأيمن) --}}
        <div class="col-lg-6 col-md-12">

            {{-- 1. العنوان الأساسي للمنتج --}}
            <h1 class="mb-2 fw-bolder fs-2 text-dark"
                x-text="activeVariant ? activeVariant.full_name : '{{ $product->getTranslation('name', app()->getLocale()) }}'">
            </h1>

            {{-- 2. عرض سمات المتغير النشط --}}
            <template x-if="activeVariant">
                <div class="mb-2">
                    <p class="small mb-1 fw-bold text-navy">
                        <template x-for="(attr, index) in activeVariant.attributes" :key="index">
                            <span x-text="`${attr.name}: ${attr.value}`" class="me-3 border-end pe-3"></span>
                        </template>
                    </p>
                </div>
            </template>

            {{-- العلامة التجارية والفئة --}}
            <p class="text-muted mb-4 small border-bottom pb-2">
                {{ __('web.category') }}: <span
                    class="text-navy fw-medium">{{ $product->category->getTranslation('name', app()->getLocale()) ?? '-' }}</span>
                | {{ __('web.brand') }}: <span
                    class="text-navy fw-medium">{{ $product->brand->getTranslation('name', app()->getLocale()) ?? '-' }}</span>
            </p>

            {{-- السعر: استخدام اللون الأحمر القوي (Deep Red) --}}
            <div class="mb-4 pb-3 border-bottom">
                {{-- سعر الـ Variant النشط --}}
                <template x-if="activeVariant">
                    <h3 class="fw-bolder fs-2 mb-1 text-deep-red">
                        {{ __('web.price') }}: <span x-text="activeVariant.price"></span>
                        {{ $product->currency->code ?? 'EGP' }}
                    </h3>
                </template>

                {{-- السعر الأساسي أو مدى الأسعار بدون Variant نشط --}}
                <template x-if="!activeVariant">
                    @if ($product->has_variants)
                        <h3 class="fw-bolder fs-2 mb-1 text-deep-red">
                            @if ($product->has_discount)
                                <span class="text-deep-red">

                                    {{ number_format($product->minVariantPrice() - ($product->minVariantPrice() * $product->discount_percentage) / 100, 2) }}
                                </span>
                            @else
                                <span class="text-dark">
                                    {{ number_format($product->minVariantPrice(), 2) }}
                                </span>
                            @endif
                            {{ $product->currency->code ?? 'EGP' }} +
                            <span class="small text-muted">{{ __('web.starting_from') }}</span>
                        </h3>
                    @else
                        @if ($product->has_discount)
                            <h3 class="fw-bolder fs-2 mb-1 text-deep-red">
                                {{ number_format($product->base_price - ($product->base_price * $product->discount_percentage) / 100, 2) }}
                                {{ $product->currency->code ?? 'EGP' }}
                            </h3>
                            <span class="text-muted text-decoration-line-through small">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency->code ?? 'EGP' }}
                            </span>
                            <span class="badge bg-danger ms-2">{{ __('web.discount') }}
                                -{{ $product->discount_percentage }}%</span>
                        @else
                            <h3 class="fw-bolder fs-2 mb-1 text-dark">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency->code ?? 'EGP' }}
                            </h3>
                        @endif
                    @endif
                </template>
                <p class="text-success small fw-bold mt-2 mb-0">✅ متوفر في المخزون (قد يختلف حسب المتغير)</p>
            </div>

            {{-- اختيار الـ Variants: خلفية رمادية فاتحة --}}
            @if ($product->variants->count())
                <h5 class="mb-3 fs-6 fw-bold text-dark">{{ __('web.available_variants') }}</h5>
                <div class="d-flex flex-wrap gap-2 mb-4 p-3 border rounded bg-light-gray">
                    @foreach ($product->variants as $variant)
                        {{-- إعداد البيانات بصيغة JSON نظيفة --}}
                        @php
                            $variantName =
                                $product->getTranslation('name', app()->getLocale()) .
                                ' - ' .
                                $variant->getTranslation('name', app()->getLocale());
                            $variantPrice = $product->has_discount
                                ? number_format(
                                    $variant->price - ($variant->price * $product->discount_percentage) / 100,
                                    2,
                                )
                                : number_format($variant->price, 2);

                            $variantImages = $variant->variantImages->map(fn($vimg) => url('storage/' . $vimg->path));

                            $attributes = $variant->attributeValues->map(
                                fn($val) => [
                                    'name' => $val->attribute->getTranslation('name', app()->getLocale()),
                                    'value' => $val->value,
                                ],
                            );

                            $variantHighlights = trim(
                                strip_tags($variant->getTranslation('highlights', app()->getLocale()), '<ul><li><ol>'),
                            );
                            $variantDrawbacks = trim(
                                strip_tags($variant->getTranslation('drawbacks', app()->getLocale()), '<ul><li><ol>'),
                            );

                            $variantData = [
                                'id' => $variant->id,
                                'full_name' => $variantName,
                                'price' => $variantPrice,
                                'images' => $variantImages->toArray(),
                                'attributes' => $attributes->toArray(),
                                'quantity' => $variant->quantity ?? 'null',
                                'highlights' => $variantHighlights,
                                'drawbacks' => $variantDrawbacks,
                            ];
                        @endphp

                        <div class="p-2 border rounded cursor-pointer transition shadow-sm text-center flex-grow-1"
                            {{-- استخدام border-info للتحديد --}}
                            :class="activeVariant && activeVariant.id === {{ $variant->id }} ?
                                'border-info border-2 bg-white' : 'border-secondary bg-white hover-shadow'"
                            style="min-width: 100px; cursor: pointer; flex-basis: 30%;"
                            @click="setActiveVariantAndImage({{ json_encode($variantData) }})"
                            wire:click='selectVariant({{ $variant->id }})'>

                            @if ($variant->variantImages->count())
                                <img src="{{ url('storage/' . $variant->variantImages->first()->path) }}"
                                    alt="Variant Image" class="rounded mb-1"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            @endif

                            <p class="mb-0 small fw-bold text-dark">
                                @foreach ($variant->attributeValues as $val)
                                    {{ $val->value }}
                                @endforeach
                            </p>
                            <p class="mb-0 small"
                                :class="activeVariant && activeVariant.id === {{ $variant->id }} && activeVariant.quantity >
                                    0 ? 'text-success' : 'text-secondary'">
                                <span
                                    class="fw-bold">{{ $variant->quantity ? $variant->quantity . ' ' . __('web.in_stock') : __('web.out_of_stock') }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- حقل الكمية وزر الإضافة للسلة (CTA - الذهبي) --}}
            <div class="mb-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fs-6 fw-bold text-dark">{{ __('web.quantity') }}:</h5>
                    {{-- حقل الكمية بتصميم نظيف --}}
                    <div class="input-group border rounded shadow-sm" style="width: 150px;">
                        <button class="btn btn-outline-secondary border-0" type="button"
                            wire:click='decrementQuantity'>
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" class="form-control text-center fw-bolder border-0" min="1"
                            value="1" wire:model='quantity'>
                        <button class="btn btn-outline-secondary border-0" type="button"
                            wire:click='incrementQuantity'>
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>

                {{-- زر الإضافة للسلة باللون الذهبي البارز --}}
                <div class="mt-4">
                    <button class="btn btn-lg btn-gold w-100 fw-bolder shadow-lg transition"
                        wire:click="addToCart({{ $product->id }})" {{-- تعطيل الزر في حال عدم اختيار Variant والمنتج يحتوي على Variants --}}
                        :disabled="!activeVariant && {{ $product->variants->count() }} > 0"
                        x-bind:class="activeVariant && activeVariant.quantity === 0 ? 'btn-secondary' : 'btn-gold'">
                        <i class="bi bi-cart-plus me-2"></i>
                        <span
                            x-text="activeVariant && activeVariant.quantity === 0 ? '{{ __('web.out_of_stock') }}' : '{{ __('web.add_to_cart') }}'"></span>
                    </button>
                    <p class="text-danger small mt-2 fw-bold"
                        x-show="!activeVariant && {{ $product->variants->count() }} > 0">
                        * الرجاء اختيار متغير (Variant) للمتابعة.
                    </p>
                </div>
            </div>

            {{-- نقاط المنتج الرئيسية (Bullet Points) --}}
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-2 border-bottom pb-2"> الملخص:</h6>
                <ul class="list-unstyled small">
                    @php
                        $cleanDescription = strip_tags(
                            $product->getTranslation('description', app()->getLocale()),
                            '<li>',
                        );
                        // محاولة استخراج النقاط من الوصف إذا كانت في صيغة قائمة
                        $points = preg_split(
                            '/(<li[^>]*>.*?<\/li>)/is',
                            $cleanDescription,
                            -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY,
                        );
                        $count = 0;
                    @endphp
                    @foreach (array_slice($points, 0, 4) as $point)
                        @if (!empty(trim(strip_tags($point))) && str_starts_with(trim($point), '<li'))
                            <li><strong class="text-info fs-5">&bull;</strong> {!! trim(strip_tags($point)) !!}</li>
                            @php $count++; @endphp
                        @endif
                    @endforeach
                    @if ($count === 0)
                        <li><strong class="text-info fs-5">&bull;</strong> {!! Str::limit(strip_tags($product->getTranslation('description', app()->getLocale())), 150) !!}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <hr class="my-5">

    {{-- Tabs للوصف والتفاصيل (أسفل الصفحة) --}}
    <div class="mt-5">
        @php
            $productHighlights = trim(
                strip_tags($product->getTranslation('highlights', app()->getLocale()), '<ul><li><ol>'),
            );
            $productDrawbacks = trim(
                strip_tags($product->getTranslation('drawbacks', app()->getLocale()), '<ul><li><ol>'),
            );
        @endphp

        <ul class="nav nav-tabs border-bottom-0" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark rounded-0 border-top-0 border-end-0 border-start-0"
                    id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab"
                    aria-selected="true">
                    {{ __('web.description') }}
                </button>
            </li>
            {{-- Tab المميزات --}}
            @if ($productHighlights)
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark rounded-0 border-top-0 border-end-0 border-start-0"
                        id="highlights-tab" data-bs-toggle="tab" data-bs-target="#highlights" type="button"
                        role="tab" aria-selected="false">
                        {{ __('web.highlights') }} <i class="bi bi-check-circle-fill text-success"></i>
                    </button>
                </li>
            @endif

            {{-- Tab العيوب --}}
            @if ($productDrawbacks)
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark rounded-0 border-top-0 border-end-0 border-start-0"
                        id="drawbacks-tab" data-bs-toggle="tab" data-bs-target="#drawbacks" type="button"
                        role="tab" aria-selected="false">
                        {{ __('web.drawbacks') }} <i class="bi bi-x-octagon-fill text-danger"></i>
                    </button>
                </li>
            @endif

            @if ($product->dataSheets->count())
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark rounded-0 border-top-0 border-end-0 border-start-0"
                        id="dataSheets-tab" data-bs-toggle="tab" data-bs-target="#dataSheets" type="button"
                        role="tab" aria-selected="false">
                        {{ __('web.data_sheet') }}
                    </button>
                </li>
            @endif
        </ul>

        <div class="tab-content border border-top-0 p-4 rounded-bottom shadow-sm bg-light-gray"
            id="productTabContent">
            {{-- Description --}}
            <div class="tab-pane fade show active" id="desc" role="tabpanel">
                <p>{!! $product->getTranslation('description', app()->getLocale()) !!}</p>
            </div>

            {{-- Highlights (المميزات) --}}
            <div class="tab-pane fade" id="highlights" role="tabpanel">
                <h5 class="fw-bold text-success mb-3">{{ __('web.highlights') }}</h5>
                <div class="p-3 border rounded bg-white shadow-sm"
                    x-show="
                        (activeVariant === null && '{!! $productHighlights !!}')
                        || (activeVariant && activeVariant.highlights)
                        @if (!$product->variants->count()) || '{!! $productHighlights !!}' @endif
                      "
                    x-html="activeVariant && activeVariant.highlights ? activeVariant.highlights : '{!! $product->getTranslation('highlights', app()->getLocale()) !!}'">

                    {{-- محتوى المنتج الأساسي إذا لم يتم اختيار Variant (للعرض الأولي) --}}
                    @if (!$product->variants->count())
                        {!! $product->getTranslation('highlights', app()->getLocale()) !!}
                    @endif
                </div>
                <div x-show="activeVariant === null && {{ $product->variants->count() }} > 0"
                    class="alert alert-info mt-3">
                    {{ __('web.please_select_variant_to_see_features') }}
                </div>
                <div x-show="activeVariant && !activeVariant.highlights" class="alert alert-secondary mt-3">
                    {{ __('web.no_highlights_for_this_variant') }}
                </div>
            </div>

            {{-- Drawbacks (العيوب) --}}
            <div class="tab-pane fade" id="drawbacks" role="tabpanel">
                <h5 class="fw-bold text-danger mb-3">{{ __('web.drawbacks') }}</h5>
                <div class="p-3 border rounded bg-white shadow-sm"
                    x-show="
                        (activeVariant === null && '{!! $productDrawbacks !!}')
                        || (activeVariant && activeVariant.drawbacks)
                        @if (!$product->variants->count()) || '{!! $productDrawbacks !!}' @endif
                      "
                    x-html="activeVariant && activeVariant.drawbacks ? activeVariant.drawbacks : '{!! $product->getTranslation('drawbacks', app()->getLocale()) !!}'">

                    {{-- محتوى المنتج الأساسي إذا لم يتم اختيار Variant (للعرض الأولي) --}}
                    @if (!$product->variants->count())
                        {!! $product->getTranslation('drawbacks', app()->getLocale()) !!}
                    @endif
                </div>
                <div x-show="activeVariant === null && {{ $product->variants->count() }} > 0"
                    class="alert alert-info mt-3">
                    {{ __('web.please_select_variant_to_see_drawbacks') }}
                </div>
                <div x-show="activeVariant && !activeVariant.drawbacks" class="alert alert-secondary mt-3">
                    {{ __('web.no_drawbacks_for_this_variant') }}
                </div>
            </div>

            {{-- Data Sheets --}}
            @if ($product->dataSheets->count())
                <div class="tab-pane fade" id="dataSheets"role="tabpanel">
                    <div class="row g-4">
                        @foreach ($product->dataSheets as $dataSheet)
                            <div class="col-lg-4 col-md-6">
                                <div
                                    class="download-card p-4 border rounded text-center shadow-sm h-100 d-flex flex-column justify-content-between bg-white">

                                    <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-3"></i>

                                    <h4 class="mb-3 text-dark fs-5 fw-bold">
                                        {{ $dataSheet->getTranslation('name', app()->getLocale()) }}
                                    </h4>

                                    <a href="{{ url('storage/' . $dataSheet->file_path) }}" target="_blank"
                                        class="btn btn-outline-dark mt-auto my-2 fw-bold text-navy"
                                        style="border-color: #003366;">
                                        {{ __('web.preview') }}
                                    </a>

                                    <a href="{{ url('storage/' . $dataSheet->file_path) }}"
                                        class="btn btn-primary mt-auto fw-bold"
                                        style="background-color: #003366; border-color: #003366;" download>
                                        <i class="bi bi-download me-1"></i> {{ __('web.download') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif

        </div>
    </div>

    {{-- المنتجات ذات الصلة --}}
    <div class="mt-5">
        <h3 class="mb-4 fw-bold text-dark" style="border-bottom: 2px solid #CC9900; padding-bottom: 5px;">
            {{ __('web.related_products') }}</h3>
        <div class="container">
            <div class="row g-4">
                @if (isset($relatedProducts) && count($relatedProducts) > 0)
                    @foreach ($relatedProducts as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 d-flex">
                            <div
                                class="post-list lg border p-3 rounded shadow-sm d-flex flex-column w-100 bg-white transition">

                                {{-- صورة المنتج --}}
                                <div class="position-relative">
                                    @if ($product->firstImage)
                                        <a href="{{ route('product.details', $product->slug) }}">
                                            <img src="{{ url('storage/' . $product->firstImage->path) }}"
                                                class="img-fluid mb-3 rounded" alt="Product Image"
                                                style="height: 200px; object-fit: cover; width: 100%;">
                                        </a>
                                    @endif

                                    {{-- لو في خصم --}}
                                    @if ($product->has_discount)
                                        <div class="discount-badge position-absolute top-0 end-0 text-white px-2 py-1 rounded-bottom-left fw-bold"
                                            style="background-color: #B22222; font-size: 0.8rem;">
                                            -{{ $product->discount_percentage }}%
                                        </div>
                                    @endif
                                </div>

                                {{-- الميتا --}}
                                <div class="post-meta small text-muted mb-2">
                                    <span class="me-2 text-info">
                                        {{ $product->category->getTranslation('name', app()->getLocale()) ?? '' }}
                                    </span> |
                                    <span class="text-dark">
                                        {{ $product->brand->getTranslation('name', app()->getLocale()) ?? '' }}
                                    </span>
                                </div>

                                {{-- اسم المنتج --}}
                                <h2 class="h6 mt-1 fw-bold">
                                    <a href="{{ route('product.details', $product->slug) }}"
                                        class="text-dark text-decoration-none hover-text-navy">
                                        {{ Str::limit($product->getTranslation('name', app()->getLocale()), 40) }}
                                    </a>
                                </h2>

                                {{-- الوصف --}}
                                <p class="flex-grow-1 text-muted small mb-3">
                                    {!! Str::limit(strip_tags($product->getTranslation('description', app()->getLocale())), 70) !!}
                                </p>

                                {{-- السعر --}}
                                <div class="price mt-auto mb-3">
                                    @if ($product->has_variants)
                                        <p class="text-navy fw-bold small">{{ __('web.has_variants') }}</p>
                                    @else
                                        @if ($product->has_discount)
                                            <div>
                                                <span class="small ms-2 text-deep-red fw-bold">
                                                    {{ number_format($product->base_price - ($product->base_price * $product->discount_percentage) / 100, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                                </span>
                                                <span class="text-muted text-decoration-line-through small ms-2">
                                                    {{ number_format($product->base_price, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                                </span>
                                            </div>
                                        @else
                                            <div>
                                                <span class="fw-bold text-dark">
                                                    {{ number_format($product->base_price, 2) . ' ' . ($product->currency->code ?? 'EGP') }}
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                {{-- زرار التفاصيل --}}
                                <a href="{{ route('product.details', $product->slug) }}"
                                    class="btn btn-sm btn-outline-dark mt-auto align-self-start fw-bold text-navy"
                                    style="border-color: #003366;">
                                    {{ __('web.view_product') }} <i class="bi bi-arrow-left"></i>
                                </a>

                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('web.no_related_products_found') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    // ❌ مُستمع لخطأ اختلاف العملة
    document.addEventListener('error_cart_currency_mismatch', event => {
        console.log(event);
        toastr.error(event.detail.message, '⚠️ خطأ في العملة');
    });

    // ❌ مُستمع لخطأ عدم اختيار المتغيرات
    document.addEventListener('error_cart_variants', event => {
        console.log(event);
        toastr.error(event.detail.message, '⚠️ اختر متغير');
    });

    // ❌ مُستمع لخطأ الكمية
    document.addEventListener('error_cart_quantity', event => {
        console.log(event);
        toastr.error(event.detail.message, '⚠️ الكمية غير متاحة');
    });

    // ✅ مُستمع لنجاح تحديث السلة
    document.addEventListener('cart_updated', event => {
        console.log(event);
        toastr.success(event.detail.message, '✅ تم بنجاح');
    });
</script>
