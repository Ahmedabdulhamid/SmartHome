<div x-data="{
    activeImage: '{{ count($product->images) ? url('storage/' . $product->images->first()->path) : '' }}',
    activeVariant: null,

    // دالة لدمج جميع صور المنتج الأساسية وصور الـ Variants في قائمة واحدة لعرضها كـ Thumbnails
    getAllImages: function() {
        // صور المنتج الأساسية
        let images = [
            @foreach ($product->images as $image)
                '{{ url('storage/' . $image->path) }}', @endforeach
        ];

        // صور جميع الـ Variants
        @foreach ($product->variants as $variant)
            @foreach ($variant->variantImages as $vimg)
                images.push('{{ url('storage/' . $vimg->path) }}'); @endforeach
        @endforeach

        // إزالة الصور المكررة والفرز الفريد
        return [...new Set(images)];
    },

    // دالة لتحديث الصورة النشطة والـ Variant النشط
    setActiveVariantAndImage: function(variantData) {
        this.activeVariant = variantData;

        if (variantData.images.length) {
            this.activeImage = variantData.images[0];
        } else {
            // العودة إلى الصورة الأساسية للمنتج إذا لم يكن للـ Variant صور
            this.activeImage = '{{ count($product->images) ? url('storage/' . $product->images->first()->path) : '' }}';
        }
    }

}" class="container py-5">
    <div class="row g-5 container">

        {{-- منطقة الصور (العمود الأيسر) --}}
        <div class="col-md-5">
            <div class="d-flex">
                {{-- الصور المصغرة العمودية على اليسار (Thumbnails) --}}
                <div class="d-flex flex-column gap-2 me-3" style="max-height: 500px; overflow-y: auto;">
                    {{-- نستخدم getAllImages لعرض جميع صور المنتج والـ Variants --}}
                    <template x-for="(img, index) in getAllImages()" :key="index">
                        <img :src="img" alt="Thumbnail" class="rounded border transition p-1"
                            :class="activeImage === img ? 'border-primary border-2 shadow' : 'border-light opacity-75'"
                            style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                            @click="activeImage = img">
                    </template>
                </div>

                {{-- الصورة الرئيسية الكبيرة --}}
                <div class="flex-grow-1 rounded border p-2 bg-white text-center shadow-sm" style="max-width: 90%;">
                    <img :src="activeImage" class="img-fluid rounded"
                        style="max-height: 500px; object-fit: contain; width: 100%;" alt="Main Image">
                </div>
            </div>
        </div>

        {{-- تفاصيل المنتج والسعر واختيار الـ Variants (العمود الأوسط) --}}
        <div class="col-md-7">

            {{-- 1. العنوان الأساسي للمنتج (باستخدام full_name) --}}
            <h1 class="mb-1 fw-normal fs-3"
                x-text="activeVariant ? activeVariant.full_name : '{{ $product->getTranslation('name', app()->getLocale()) }}'">
            </h1>

            {{-- 2. عرض سمات المتغير النشط --}}
            <template x-if="activeVariant">
                <div class="mb-2">
                    <p class="text-dark small mb-1 fw-bold">
                        <template x-for="(attr, index) in activeVariant.attributes" :key="index">
                            <span x-text="`${attr.name}: ${attr.value}`" class="me-3 text-primary"></span>
                        </template>
                    </p>
                </div>
            </template>

            {{-- العلامة التجارية والفئة --}}
            <p class="text-secondary mb-3 small border-bottom pb-2">
                {{ __('web.category') }}: <span
                    class="text-info">{{ $product->category->getTranslation('name', app()->getLocale()) ?? '-' }}</span>
                | {{ __('web.brand') }}: <span
                    class="text-info">{{ $product->brand->getTranslation('name', app()->getLocale()) ?? '-' }}</span>
            </p>

            {{-- السعر --}}
            <div class="mb-4 pb-3 border-bottom">
                {{-- سعر الـ Variant النشط --}}
                <template x-if="activeVariant">
                    <h3 class="fw-bold text-danger fs-3 mb-1">
                        {{ __('web.price') }}: <span x-text="activeVariant.price"></span>
                        {{ $product->currency->code ?? 'EGP' }}
                    </h3>
                </template>

                {{-- السعر الأساسي أو مدى الأسعار بدون Variant نشط --}}
                <template x-if="!activeVariant">
                    @if ($product->has_variants)
                        <h3 class="fw-bold text-dark fs-3 mb-1">
                            {{ __('web.price') }}:
                            @if ($product->has_discount)
                                <span class="text-danger">

                                    {{ number_format($product->minVariantPrice() - ($product->minVariantPrice() * $product->discount_percentage) / 100, 2) }}
                                </span>
                            @else

                                <span class="text-dark">
                                    {{ number_format($product->minVariantPrice(), 2) }}
                                </span>
                            @endif
                            {{ $product->currency->code ?? 'EGP' }}
                        </h3>
                    @else
                        @if ($product->has_discount)
                            <h3 class="fw-bold text-danger fs-3 mb-1">
                                {{ number_format($product->base_price - ($product->base_price * $product->discount_percentage) / 100, 2) }}
                                {{ $product->currency->code ?? 'EGP' }}
                            </h3>
                            <span class="text-muted text-decoration-line-through small">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency->code ?? 'EGP' }}
                            </span>
                        @else
                            <h3 class="fw-bold text-dark fs-3 mb-1">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency->code ?? 'EGP' }}
                            </h3>
                        @endif
                    @endif
                </template>
                <p class="text-success small fw-bold mt-2 mb-0">متوفر في المخزون</p>
            </div>

            {{-- 💡 الإضافة المطلوبة: حقل الكمية وزر الإضافة للسلة (Static Placeholder) --}}
            <div class="mb-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fs-6 fw-bold text-dark">{{ __('web.quantity') }}:</h5>
                    {{-- حقل الكمية الثابت (سيتولى Livewire تحديثه لاحقًا) --}}
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" wire:click='decrementQuantity'>
                            <i class="bi bi-dash"></i>
                        </button>
                        {{-- قيمة ثابتة الآن. يمكنك تغييرها إلى model:quantityx-model.number="quantity" عند تفعيل Livewire --}}
                        <input type="number" class="form-control text-center fw-bold" min="1" value="1" wire:model='quantity' >
                        <button class="btn btn-outline-secondary" type="button" wire:click='incrementQuantity'>
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>

                {{-- زر الإضافة للسلة الثابت (سيتولى Livewire ربط منطق الإضافة به لاحقًا) --}}
                <div class="mt-4">
                    <button class="btn btn-lg btn-success w-100 fw-bold shadow-lg transition" wire:click="addToCart({{ $product->id }})">
                        <i class="bi bi-cart-plus me-2"></i> {{ __('web.add_to_cart') }}
                    </button>
                </div>
            </div>
            {{-- نهاية الإضافة المطلوبة --}}


            {{-- **الجزء المنقول** (اختيار الـ Variants) --}}
            @if ($product->variants->count())
                <h5 class="mb-3 fs-6 fw-bold text-dark">{{ __('web.available_variants') }}</h5>
                <div class="d-flex flex-wrap gap-2 mb-4 p-3 border rounded bg-light">
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

                            // 💡 التعديل: إضافة المميزات والعيوب للـ Variant
                            $variantHighlights = trim(
                                strip_tags($variant->getTranslation('highlights', app()->getLocale())),
                            ); // نستخدم trim/strip_tags لتنظيف البيانات
                            $variantDrawbacks = trim(
                                strip_tags($variant->getTranslation('drawbacks', app()->getLocale())),
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

                        <div class="p-2 border rounded cursor-pointer transition shadow-sm text-center"
                            :class="activeVariant && activeVariant.id === {{ $variant->id }} ?
                                'border-primary border-2 bg-white' : 'border-secondary bg-white'"
                            style="min-width: 100px; cursor: pointer;"
                            @click="setActiveVariantAndImage({{ json_encode($variantData) }})"  wire:click='selectVariant({{ $variant->id }})'>

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
                            <p class="mb-0 small text-success">
                                <span
                                    class="fw-bold">{{ $variant->quantity ? $variant->quantity . ' ' . __('web.in_stock') : __('web.out_of_stock') }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
            {{-- **نهاية الجزء المنقول** --}}


            {{-- نقاط المنتج الرئيسية (Bullet Points) --}}
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-2">حول هذا المنتج:</h6>
                <ul class="list-unstyled">
                    @php
                        $cleanDescription = strip_tags(
                            $product->getTranslation('description', app()->getLocale()),
                            '<li>',
                        );
                        $points = preg_split(
                            '/<li>(.*?)<\/li>/s',
                            $cleanDescription,
                            -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY,
                        );
                        $count = 0;
                    @endphp
                    @foreach (array_slice($points, 0, 5) as $point)
                        @if (!empty(trim($point)))
                            <li><strong class="text-dark fs-5">&bull;</strong> {{ trim($point) }}</li>
                            @php $count++; @endphp
                        @endif
                    @endforeach
                    @if ($count === 0)
                        <li><strong class="text-dark fs-5">&bull;</strong> {!! Str::limit(strip_tags($product->getTranslation('description', app()->getLocale())), 150) !!}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    ---

    {{-- Tabs للوصف والتفاصيل (أسفل الصفحة) --}}
    <div class="mt-5">
        @php
            // تحقق من وجود المميزات أو العيوب للمنتج الأساسي
            $productHighlights = trim(strip_tags($product->getTranslation('highlights', app()->getLocale())));
            $productDrawbacks = trim(strip_tags($product->getTranslation('drawbacks', app()->getLocale())));
        @endphp

        <ul class="nav nav-tabs" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                {{-- الوصف يبدأ كـ Active بشكل افتراضي --}}
                <button class="nav-link active fw-bold text-dark" id="desc-tab" data-bs-toggle="tab"
                    data-bs-target="#desc" type="button" role="tab" aria-selected="true">
                    {{ __('web.description') }}
                </button>
            </li>
            @if ($product->variants->count())
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark" id="variants-tab" data-bs-toggle="tab"
                        data-bs-target="#variants" type="button" role="tab" aria-selected="false">
                        {{ __('web.variants') }}
                    </button>
                </li>
            @endif
            @if ($product->dataSheets->count())
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark" id="dataSheets-tab" data-bs-toggle="tab"
                        data-bs-target="#dataSheets" type="button" role="tab" aria-selected="false">
                        {{ __('web.data_sheet') }}
                    </button>
                </li>
            @endif

            {{-- 💡 التعديل: Tab المميزات (Highlights) - يستخدم x-show للتحكم في ظهوره ديناميكياً --}}
            <li class="nav-item" role="presentation"
                x-show="
                    (activeVariant === null && '{!! $productHighlights !!}')
                    || (activeVariant && activeVariant.highlights)
                    @if (!$product->variants->count()) && '{!! $productHighlights !!}' @endif
                ">
                <button class="nav-link fw-bold text-dark" id="highlights-tab" data-bs-toggle="tab"
                    data-bs-target="#highlights" type="button" role="tab" aria-selected="false">
                    {{ __('web.highlights') }}
                </button>
            </li>

            {{-- 💡 التعديل: Tab العيوب (Drawbacks) - يستخدم x-show للتحكم في ظهوره ديناميكياً --}}
            <li class="nav-item" role="presentation"
                x-show="
                    (activeVariant === null && '{!! $productDrawbacks !!}')
                    || (activeVariant && activeVariant.drawbacks)
                    @if (!$product->variants->count()) && '{!! $productDrawbacks !!}' @endif
                ">
                <button class="nav-link fw-bold text-dark" id="drawbacks-tab" data-bs-toggle="tab"
                    data-bs-target="#drawbacks" type="button" role="tab" aria-selected="false">
                    {{ __('web.drawbacks') }}
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-4 rounded-bottom" id="productTabContent">
            {{-- Description --}}
            <div class="tab-pane fade show active" id="desc" role="tabpanel">
                <p>{!! $product->getTranslation('description', app()->getLocale()) !!}</p>
            </div>

            {{-- Variants (عرض تفصيلي داخل الـ Tab) --}}
            @if ($product->variants->count())
                <div class="tab-pane fade" id="variants" role="tabpanel">
                    <div class="row g-3">
                        @foreach ($product->variants as $variant)
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-light">
                                    @if ($variant->variantImages->count())
                                        <img src="{{ url('storage/' . $variant->variantImages->first()->path) }}"
                                            class="card-img-top" style="height: 180px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title text-dark">
                                            {{ $variant->getTranslation('name', app()->getLocale()) }}
                                        </h5>
                                        <p class="fw-bold text-success mb-1">
                                            @if ($product->has_discount)
                                                <del
                                                    class='text-small text-secondary'><small>{{ number_format($variant->price, 2) }}</small></del>
                                                <span
                                                    class="fs-5">{{ number_format($variant->price * (1 - $product->discount_percentage / 100), 2) }}</span>
                                                {{ $product->currency->code ?? 'EGP' }}
                                            @else
                                                <span class="fs-5">{{ number_format($variant->price, 2) }}</span>
                                                {{ $product->currency->code ?? 'EGP' }}
                                            @endif
                                        </p>
                                        <p class="small text-muted mb-1">
                                            @foreach ($variant->attributeValues as $val)
                                                <strong>{{ $val->attribute->getTranslation('name', app()->getLocale()) }}</strong>
                                                :
                                                {{ $val->value }}<br>
                                            @endforeach
                                        </p>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Data Sheets --}}
            @if ($product->dataSheets->count())
                <div class="tab-pane fade" id="dataSheets"role="tabpanel">
                    <div class="row g-3">
                        @foreach ($product->dataSheets as $dataSheet)
                            <div class="col-lg-4 col-md-6">
                                <div
                                    class="download-card p-4 border rounded text-center shadow-sm h-100 d-flex flex-column justify-content-between bg-white">

                                    <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-3"></i>

                                    <h4 class="mb-3 text-dark fs-5">
                                        {{ $dataSheet->getTranslation('name', app()->getLocale()) }}
                                    </h4>

                                    <a href="{{ url('storage/' . $dataSheet->file_path) }}" target="_blank"
                                        class="btn btn-outline-primary mt-auto my-2 fw-bold">
                                        {{ __('web.preview') }}
                                    </a>

                                    <a href="{{ url('storage/' . $dataSheet->file_path) }}"
                                        class="btn btn-primary mt-auto fw-bold" download>
                                        <i class="bi bi-download me-1"></i> {{ __('web.download') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif

            {{-- 💡 التعديل: Highlights (المميزات) --}}
            {{-- نستخدم شرط @if للتحقق الأولي، و x-show داخل المحتوى ليظهر إما المحتوى أو رسالة "يرجى الاختيار" --}}
            @if ($productHighlights || $product->variants->count())
                <div class="tab-pane fade" id="highlights" role="tabpanel">
                    <h5 class="fw-bold text-success mb-3">{{ __('web.highlights') }} <i class="bi bi-star-fill"></i>
                    </h5>

                    {{-- عرض المميزات --}}
                    <div class="p-3 border rounded bg-light min-h-200"
                        x-show="
                            (activeVariant === null && '{!! $productHighlights !!}')
                            || (activeVariant && activeVariant.highlights)
                          "
                        x-html="activeVariant && activeVariant.highlights ? activeVariant.highlights : '{!! $product->getTranslation('highlights', app()->getLocale()) !!}'">

                        {{-- محتوى المنتج الأساسي إذا لم يتم اختيار Variant (للعرض الأولي) --}}
                        @if (!$product->variants->count())
                            {!! $product->getTranslation('highlights', app()->getLocale()) !!}
                        @endif
                    </div>

                    {{-- رسائل التنبيه والتشجيع --}}
                    <div x-show="activeVariant === null && {{ $product->variants->count() }} > 0"
                        class="alert alert-info mt-3">
                        {{ __('web.please_select_variant_to_see_features') }}
                    </div>

                    <div x-show="activeVariant && !activeVariant.highlights" class="alert alert-secondary mt-3">
                        {{ __('web.no_highlights_for_this_variant') }}
                    </div>
                </div>
            @endif

            {{-- 💡 التعديل: Drawbacks (العيوب) --}}
            @if ($productDrawbacks || $product->variants->count())
                <div class="tab-pane fade" id="drawbacks" role="tabpanel">
                    <h5 class="fw-bold text-danger mb-3">{{ __('web.drawbacks') }} <i
                            class="bi bi-x-octagon-fill"></i></h5>

                    {{-- عرض العيوب --}}
                    <div class="p-3 border rounded bg-light min-h-200"
                        x-show="
                            (activeVariant === null && '{!! $productDrawbacks !!}')
                            || (activeVariant && activeVariant.drawbacks)
                          "
                        x-html="activeVariant && activeVariant.drawbacks ? activeVariant.drawbacks : '{!! $product->getTranslation('drawbacks', app()->getLocale()) !!}'">

                        {{-- محتوى المنتج الأساسي إذا لم يتم اختيار Variant (للعرض الأولي) --}}
                        @if (!$product->variants->count())
                            {!! $product->getTranslation('drawbacks', app()->getLocale()) !!}
                        @endif
                    </div>

                    {{-- رسائل التنبيه والتشجيع --}}
                    <div x-show="activeVariant === null && {{ $product->variants->count() }} > 0"
                        class="alert alert-info mt-3">
                        {{ __('web.please_select_variant_to_see_drawbacks') }}
                    </div>

                    <div x-show="activeVariant && !activeVariant.drawbacks" class="alert alert-secondary mt-3">
                        {{ __('web.no_drawbacks_for_this_variant') }}
                    </div>
                </div>
            @endif

        </div>
    </div>
    <div class="mt-5">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-5">
                @if (isset($relatedProducts) && count($relatedProducts) > 0)
                    @foreach ($relatedProducts as $product)
                        <div class="col-lg-4 d-flex">
                            <div class="post-list lg border p-3 rounded d-flex flex-column w-100">

                                {{-- صورة المنتج --}}
                                <div class="position-relative">
                                    @if ($product->firstImage)
                                        <a href="{{ route('product.details', $product->slug) }}">
                                            <img src="{{ url('storage/' . $product->firstImage->path) }}"
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
                                <div class="price mt-auto ">
                                    @if ($product->has_variants)
                                        <p class="text-primary">{{ __('web.has_variants') }}</p>
                                    @else
                                        @if ($product->has_discount)
                                            <div>
                                                <span class="text-danger small ms-2">
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
                                <a href="{{ route('product.details', $product->slug) }}"
                                    class="btn btn-sm btn-primary mt-3 align-self-start">
                                    {{ __('web.view_product') }}
                                </a>

                            </div>
                        </div>
                    @endforeach

                @endif
            </div>
        </div>

    </div>
</div>
<script>
   document.addEventListener('error_cart_variants', event => {
    toastr.error(event.detail.message);
});
document.addEventListener('error_cart_quantity', event => {
    toastr.error(event.detail.message);
});
document.addEventListener('cart_updated', event => {
    toastr.success(event.detail.message);
});
</script>
