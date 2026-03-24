@php
    use App\Models\ProductVariant;
@endphp

<div class="container">
    <div class="row mt-4">

        {{-- LEFT: Cart Items --}}
        <div class="col-md-8">

            @if(count($cartItems) > 0)
                @foreach ($cartItems as $item)
                    @php
                        $variantId = $item->pivot->product_variant_id;
                        $variant = $variantId ? ProductVariant::find($variantId) : null;
                        $product = $item;
                        $mainImage = $product->images->first();

                        // الحصول على صورة المتغير إذا وجد
                        $variantImage = null;
                        if ($variant && method_exists($variant, 'variantImages')) {
                            $variantImage = $variant->variantImages()->first();
                        }
                    @endphp

                    <div class="row py-4 border-bottom align-items-center"
                         wire:key="cart-item-{{ $item->id }}-{{ $variantId }}">

                        {{-- Product Image --}}
                        <div class="col-md-2 col-4">
                            <img
                                src="{{ $variant && $variantImage
                                    ? asset('storage/' . $variantImage->path)
                                    : ($mainImage
                                        ? asset('storage/' . $mainImage->path)
                                        : asset('assets/img/istockphoto-2173059563-612x612.jpg'))
                                }}"
                                class="img-fluid rounded border"
                                style="height: 120px; width: 100%; object-fit: contain;"
                                alt="{{ $product->getTranslation('name', app()->getLocale()) }}"
                                onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                        </div>

                        {{-- Product Name & Variant --}}
                        <div class="col-md-5 col-8">
                            <h6 class="fw-bold mb-1">
                                {{ $product->getTranslation('name', app()->getLocale()) }}
                            </h6>

                            @if($variant)
                                <p class="text-muted small mb-1">
                                    {{ $variant->getTranslation('name', app()->getLocale()) }}
                                </p>
                            @endif

                            {{-- Unit Price --}}
                            <p class="text-dark mb-1">
                                @php
                                    $unitPrice = $this->getUnitPrice($product->id, $variantId);
                                @endphp
                                {{ number_format($unitPrice, 2) }}
                                {{ $product->currency->code }} each
                            </p>

                            {{-- Delete Button --}}
                            <button
                                class="btn btn-sm text-danger p-0 mt-2"
                                wire:click="removeFromCart({{ $product->id }}, {{ $variant ? $variant->id : 'null' }})"
                                wire:loading.attr="disabled"
                                wire:target="removeFromCart"
                            >
                                <i class="bi bi-trash3 fs-6"></i>
                                <span wire:loading.remove wire:target="removeFromCart">Remove</span>
                                <span wire:loading wire:target="removeFromCart">Removing...</span>
                            </button>
                        </div>

                        {{-- Quantity Controls --}}
                        <div class="col-md-3 col-12 mt-3 mt-md-0 d-flex align-items-center justify-content-center">

                            {{-- Decrease Button --}}
                            <button class="btn btn-outline-dark btn-sm"
                                wire:click="decreaseQty({{ $product->id }}, {{ $variant ? $variant->id : 'null' }})"
                                wire:loading.attr="disabled"
                                wire:target="decreaseQty">

                                <span wire:loading.remove wire:target="decreaseQty">
                                    <i class="bi bi-dash"></i>
                                </span>
                                <span wire:loading wire:target="decreaseQty">
                                    <i class="bi bi-arrow-repeat spinner"></i>
                                </span>
                            </button>

                            {{-- Quantity Display --}}
                            <div class="mx-3">
                                <span class="fw-bold fs-6 d-block text-center">
                                    <span wire:loading.remove wire:target="decreaseQty,increaseQty,updateQuantity">
                                        {{ $item->pivot->quantity }}
                                    </span>
                                    <span wire:loading wire:target="decreaseQty,increaseQty,updateQuantity" class="text-muted">
                                        <i class="bi bi-arrow-repeat spinner"></i>
                                    </span>
                                </span>
                            </div>

                            {{-- Increase Button --}}
                            <button class="btn btn-outline-dark btn-sm"
                                wire:click="increaseQty({{ $product->id }}, {{ $variant ? $variant->id : 'null' }})"
                                wire:loading.attr="disabled"
                                wire:target="increaseQty">

                                <span wire:loading.remove wire:target="increaseQty">
                                    <i class="bi bi-plus"></i>
                                </span>
                                <span wire:loading wire:target="increaseQty">
                                    <i class="bi bi-arrow-repeat spinner"></i>
                                </span>
                            </button>
                        </div>

                        {{-- Total Price for this Item --}}
                        <div class="col-md-2 text-end mt-3 mt-md-0">
                            <h5 class="text-dark fw-bold">
                                <span wire:loading.remove wire:target="decreaseQty,increaseQty,updateQuantity">
                                    {{ number_format($item->pivot->price, 2) }} {{ $product->currency->code }}
                                </span>
                                <span wire:loading wire:target="decreaseQty,increaseQty,updateQuantity" class="text-muted">
                                    <i class="bi bi-arrow-repeat spinner"></i>
                                </span>
                            </h5>
                        </div>

                    </div>
                @endforeach
            @else
                {{-- Empty Cart Message --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-cart-x" style="font-size: 4rem; color: #6c757d;"></i>
                    </div>
                    <h4 class="text-muted mb-3">سلة التسوق فارغة</h4>
                    <p class="text-muted mb-4">لم تقم بإضافة أي منتجات إلى سلة التسوق بعد</p>
                    <a href="" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>
                        ابدأ التسوق
                    </a>
                </div>
            @endif

        </div>

        {{-- RIGHT: ORDER SUMMARY --}}
        <div class="col-md-4">
            <div class="card shadow-sm p-3 sticky-top" style="top: 100px; border-radius: 12px;">
                <h5 class="fw-bold mb-3">ملخص الطلب</h5>

                {{-- Subtotal --}}
                <div class="d-flex justify-content-between mb-2">
                    <span>المجموع الفرعي</span>
                    <span class="fw-semibold">
                        {{ number_format($subtotal, 2) }} {{ count($cartItems) > 0 ? $cartItems->first()->currency->code : 'EGP' }}
                    </span>
                </div>

                {{-- Shipping --}}
                <div class="d-flex justify-content-between mb-2">
                    <span>الشحن</span>
                    <span class="fw-semibold text-success">
                        مجاني
                    </span>
                </div>

                {{-- Tax --}}
                <div class="d-flex justify-content-between mb-2">
                    <span>الضريبة</span>
                    <span class="fw-semibold">
                        0.00 {{ count($cartItems) > 0 ? $cartItems->first()->currency->code : 'EGP' }}
                    </span>
                </div>

                <hr>

                {{-- Total --}}
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">المجموع الكلي</h5>
                    <h5 class="fw-bold mb-0">
                        {{ number_format($total, 2) }} {{ count($cartItems) > 0 ? $cartItems->first()->currency->code : 'EGP' }}
                    </h5>
                </div>

                {{-- Checkout Button --}}
                <button
                    class="btn btn-warning w-100 fw-bold py-2"
                    style="font-size: 16px; border-radius: 10px;"
                    wire:click="proceedToCheckout"
                    wire:loading.attr="disabled"
                    {{ count($cartItems) === 0 ? 'disabled' : '' }}>

                    <span wire:loading.remove wire:target="proceedToCheckout">
                        <i class="bi bi-lock-fill me-2"></i>
                        إتمام الشراء
                    </span>
                    <span wire:loading wire:target="proceedToCheckout">
                        <i class="bi bi-arrow-repeat spinner me-2"></i>
                        جاري المعالجة
                    </span>
                </button>

                {{-- Continue Shopping --}}
                <a href="" class="btn btn-outline-dark w-100 mt-2 py-2">
                    <i class="bi bi-arrow-left me-2"></i>
                    مواصلة التسوق
                </a>

                {{-- Cart Items Count --}}
                <div class="text-center mt-3">
                    <small class="text-muted">
                        {{ $cartItemsCount }} عناصر في السلة
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
    .spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .sticky-top {
        z-index: 10;
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>
@endpush
