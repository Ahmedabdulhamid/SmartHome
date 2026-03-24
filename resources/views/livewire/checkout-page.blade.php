@php
    use App\Models\ProductVariant;
    $cartEmpty = !isset($cartItems) || !isset($cartItems->items) || $cartItems->items->isEmpty();

    // حساب المجموع الفرعي مرة واحدة
    $subtotal = 0;
    if (!$cartEmpty) {
        foreach ($cartItems->items as $item) {
            $subtotal += ($item->pivot->price * $item->pivot->quantity);
        }
    }

    // سعر الشحن وقيمة إجمالية
    $shippingPriceValue = $shippingPrice?->price ?? 0;
    $total = $subtotal + $shippingPriceValue;

    // كود العملة الافتراضي
    $currencyCode = $cartEmpty ? 'EGP' : ($cartItems->items->first()?->currency?->code ?? 'EGP');
@endphp

<div class="checkout-page py-5 container">
    {{-- CSS styles داخل div --}}
    <style>
        :root {
            --primary-color: #4a6fff;
            --primary-dark: #3a5ce9;
            --secondary-color: #ff6b8b;
            --dark-color: #121212;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --border-color: #e1e5eb;
            --success-color: #28a745;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        .checkout-page {
            width: 100%;
        }

        .checkout-page h1 {
            font-size: 2.8rem;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .checkout-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 16px;
            border: none;
        }

        .checkout-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12) !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border: none;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--primary-dark), #ff5577);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 111, 255, 0.3);
        }

        /* Progress Bar Styles */
        .progress-container .step {
            text-align: center;
            position: relative;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 600;
            border: 3px solid #e9ecef;
            transition: var(--transition);
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-color: var(--primary-color);
        }

        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            transition: var(--transition);
        }

        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .step-line {
            flex: 1;
            height: 3px;
            background: #e9ecef;
            margin: 0 10px;
            position: relative;
            top: -20px;
            transition: var(--transition);
        }

        .step-line.active {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        /* Order Items Scrollbar */
        .order-items::-webkit-scrollbar {
            width: 5px;
        }

        .order-items::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .order-items::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
        }

        /* Form Controls Enhancement */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid var(--border-color);
            padding: 12px 15px;
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(74, 111, 255, 0.15);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px !important;
            border: 2px solid var(--border-color);
            border-right: none;
            background-color: #f8f9fa;
        }

        /* Mobile First Responsive Design */
        @media (max-width: 576px) {
            .checkout-page {
                padding: 15px 0 !important;
                margin: 0 !important;
                width: 94vw;
                max-width: 94vw;
                overflow-x: hidden;
            }

            .checkout-page .container {
                max-width: 100% !important;
                width: 100% !important;
                padding-right: 15px !important;
                padding-left: 15px !important;
                margin-right: auto !important;
                margin-left: auto !important;
            }

            .checkout-page .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100% !important;
            }

            .checkout-page .col-12 {
                padding-left: 0 !important;
                padding-right: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .checkout-page h1 {
                font-size: 1.8rem !important;
                width: 100% !important;
                text-align: center;
                padding: 0 10px;
            }

            .checkout-page p.text-muted.fs-5 {
                font-size: 1rem !important;
                padding: 0 10px;
            }

            .progress-container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 10px;
            }

            .checkout-card {
                border-radius: 10px !important;
                margin-bottom: 15px !important;
                width: 100% !important;
                padding: 20px 15px !important;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
                padding: 12px !important;
            }

            .product-image {
                margin: 0 auto 10px !important;
            }

            .payment-option {
                width: 100% !important;
                margin-bottom: 8px;
                text-align: center;
                justify-content: center;
            }

            .btn-primary {
                width: 100% !important;
                padding: 15px !important;
                font-size: 1.1rem !important;
            }

            /* إزالة أي margins أو paddings زائدة */
            .checkout-page * {
                box-sizing: border-box;
            }
        }

        @media (min-width: 577px) and (max-width: 768px) {
            .checkout-page .container {
                max-width: 100% !important;
                padding-right: 20px !important;
                padding-left: 20px !important;
            }

            .checkout-page h1 {
                font-size: 2.2rem !important;
            }

            .checkout-card {
                padding: 25px !important;
            }

            .progress-container .d-flex {
                flex-wrap: wrap;
                justify-content: center;
            }

            .step-line {
                display: none;
            }

            .step {
                margin: 0 10px;
            }

            .payment-option {
                flex: 1;
                min-width: 120px;
                text-align: center;
            }
        }

        @media (min-width: 769px) {
            .checkout-page .container {
                max-width: 1200px;
            }
        }
    </style>

    <div class="container px-0">
    {{-- Header Section --}}
    <div class="text-center mb-5 px-3">
        <h1 class="fw-bold mb-3">{{ __('web.checkout') }}</h1>

        <p class="text-muted fs-5">{{ __('web.complete_your_purchase_with_confidence') }}</p>

        {{-- Progress Bar --}}
        <div class="progress-container mt-4 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="step active">
                    <div class="step-circle">1</div>
                    <div class="step-label">{{ __('web.cart') }}</div>
                </div>
                <div class="step-line active"></div>
                <div class="step active">
                    <div class="step-circle">2</div>
                    <div class="step-label">{{ __('web.details') }}</div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <div class="step-label">{{ __('web.payment') }}</div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <div class="step-label">{{ __('web.confirm') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Conditional Form Display --}}
    @if (!$cartEmpty)
        <form wire:submit.prevent='submit'>
            <div class="row g-4 mx-0 container">
                {{-- Order Summary Column --}}
                <div class="col-12 col-lg-6 col-md-6 col-sm-12 px-3">
                    <div class="card shadow-lg p-4 rounded-4 border-0 h-100 checkout-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3"
                                style="background: linear-gradient(135deg, #28a745, #20c997); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shopping-bag text-white"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">{{ __('web.order_summery') }}</h4>
                        </div>

                        <div class="order-items mb-4" style="max-height: 300px; overflow-y: auto;">
                            {{-- تم استبدال شرط @if (isset($cartItems)) بشرط !cartEmpty --}}
                            @foreach ($cartItems->items as $item)
                                @php
                                    $variantId = $item->pivot->product_variant_id;
                                    // استخدام Nullsafe operator
                                    $variant = $variantId ? ProductVariant::find($variantId) : null;
                                    $product = $item;
                                @endphp
                                <div class="order-item d-flex align-items-center mb-3 p-3 rounded-3"
                                    style="background-color: #f8f9fa;">
                                    <div class="product-image me-3"
                                        style="width: 60px; height: 60px; background: linear-gradient(135deg, #e3f2fd, #f3e5f5); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-box text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <strong
                                                class="product-name">{{ $product->getTranslation('name', app()->getLocale()) }}</strong>
                                            <span
                                                class="fw-bold text-primary">{{ number_format($item->pivot->price, 2) }}
                                                {{ $currencyCode }}</span>
                                        </div>
                                        @if ($variant)
                                            <div class="text-muted small">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $variant->getTranslation('name', app()->getLocale()) }}
                                            </div>
                                        @endif
                                        <div class="text-muted small">
                                            <i class="fas fa-layer-group me-1"></i>
                                            {{ __('web.quantity') }}: {{ $item->pivot->quantity }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        {{-- Price Summary --}}
                        <div class="price-summary mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('web.subtotal') }}</span>
                                <span class="fw-semibold">
                                    {{-- استخدام المتغيرات المحسوبة في الأعلى --}}
                                    {{ number_format($subtotal, 2) }}
                                    {{ $currencyCode }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('web.shipping') }}</span>

                                <span class="fw-semibold">
                                    {{-- استخدام Nullsafe operator والقيمة الافتراضية --}}
                                    {{ number_format($shippingPriceValue, 2) }}
                                    {{ $shippingPrice?->currency?->code ?? $currencyCode }}
                                </span>


                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('web.estimated_days') }}</span>
                                <span>
                                    {{-- فحص وجود $shippingPrice --}}
                                    @if ($shippingPrice)
                                        <small>{{ __('web.shipping_arrival', ['days' => $estimated_days]) }}</small>
                                    @else
                                        <small>{{ __('web.shipping_arrival_tbd') }}</small>
                                    @endif
                                </span>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold fs-5">{{ __('web.total') }}</span>
                                <span class="fw-bold text-primary fs-5">
                                    {{-- استخدام المتغيرات المحسوبة في الأعلى --}}
                                    {{ number_format($total, 2) }}
                                    {{ $currencyCode }}
                                </span>
                            </div>
                        </div>

                        {{-- Payment Methods --}}
                        <div class="payment-methods mb-4">
                            <label class="form-label fw-semibold mb-3">{{ __('web.payment_methods') }}</label>
                            <div class="d-flex flex-wrap gap-2">

                                {{-- فحص وجود $paymMethods قبل التكرار --}}
                                @if (isset($paymMethods))
                                    @foreach ($paymMethods as $method)
                                        <label for="payment-{{ $method->id }}" class="payment-option px-3 py-2 rounded-3"
                                            style="cursor: pointer; border: 2px solid {{ $method->id == $paym_method ? 'var(--primary-color)' : '#e9ecef' }}; background-color: {{ $method->id == $paym_method ? 'rgba(74, 111, 255, 0.05)' : 'white' }}; transition: all 0.2s;">

                                            <input type="radio" id="payment-{{ $method->id }}" name="paym_method_radio"
                                                wire:model='paym_method' value="{{ $method->id }}" class="d-none"
                                                {{-- إخفاء زر الراديو الأصلي --}}>

                                            <i class="fas fa-credit-card me-2"></i>
                                            {{-- استخدام Nullsafe operator --}}
                                            <span>{{ $method?->name }}</span>
                                        </label>
                                    @endforeach
                                @endif

                            </div>

                            @error('paym_method')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Place Order Button --}}
                        <button class="btn btn-primary w-100 py-3 fs-5 fw-bold rounded-3"type='submit'>
                            <i class="fas fa-lock me-2"></i>
                            {{ __('web.place_order_securely') }}

                        </button>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                {{ __('web.secure_payment_256_bit_ssl_encyption') }}

                            </small>
                        </div>
                    </div>
                </div>
                {{-- Billing Details Column --}}
                <div class="col-12 col-lg-6 col-md-6 col-sm-12 px-3">
                    <div class="card shadow-lg p-4 rounded-4 border-0 h-100 checkout-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3"
                                style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">{{ __('web.billing_details') }}</h4>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('web.select_city') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-city text-muted"></i>
                                    </span>
                                    <select name="city" id="city" class="form-control"wire:model.live='cityId'>
                                        <option value="">{{ __('web.select') }}</option>
                                        {{-- فحص وجود $cities قبل التكرار --}}
                                        @if (isset($cities))
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">
                                                    {{ $city->getTranslation('name', app()->getLocale()) }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                                @error('cityId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('web.select_governorate') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                    </span>
                                    <select name="governorate" id="governorate"
                                        class="form-control"wire:model.live='govoernorateId'>
                                        <option value="">{{ __('web.select') }}</option>
                                        {{-- فحص وجود $governorates قبل التكرار --}}
                                        @if (isset($governorates))
                                            @foreach ($governorates as $governorate)
                                                <option value="{{ $governorate->id }}">
                                                    {{ $governorate->getTranslation('name', app()->getLocale()) }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                                @error('govoernorateId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('web.f_name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="first name"
                                        wire:model='f_name'>

                                </div>
                                @error('f_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('web.l_name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                        placeholder="last name"wire:model='l_name'>

                                </div>
                                @error('l_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('web.email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" class="form-control"
                                    placeholder="example@mail.com"wire:model='email'>

                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('web.phone') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone text-muted"></i>
                                </span>
                                <input type="tel" class="form-control" placeholder="+20 123 456 7890"
                                    wire:model='phone'>

                            </div>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('web.address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-home text-muted"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Street, Building, Apartment"
                                    wire:model='address'>

                            </div>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('web.zip_code') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-home text-muted"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Zip Code"
                                    wire:model='zip_code'>

                            </div>
                            @error('zip_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('web.shipping_type') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-map-marker-alt text-muted"></i>
                                </span>
                                <select name="governorate" id="governorate"
                                    class="form-control"wire:model.live='shipping_type'>
                                    <option value="">{{ __('web.select') }}</option>
                                    <option value="standard">{{ __('web.standard') }}</option>
                                    <option value="express">{{ __('web.express') }}</option>


                                </select>

                            </div>
                            @error('shipping_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        {{-- عرض رسالة "السلة فارغة" بدلاً من النموذج --}}
        <div class="row g-4 mx-0 container">
            <div class="col-12 px-3">
                <div class="card shadow-lg p-5 rounded-4 border-0 text-center">
                    <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                    <h4 class="text-muted">{{ __('web.your_cart_empty') }}</h4>
                    <p class="text-muted">{{ __('web.add_items_to_continue_checkout') }}</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-3 w-50 mx-auto">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('web.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>


    <script>
        document.addEventListener('livewire:load', function() {
            // Payment Method Selection
            const paymentOptions = document.querySelectorAll('.payment-option');
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    paymentOptions.forEach(opt => {
                        opt.style.borderColor = 'var(--border-color)';
                        opt.style.backgroundColor = 'transparent';
                    });
                    this.style.borderColor = 'var(--primary-color)';
                    this.style.backgroundColor = 'rgba(74, 111, 255, 0.05)';
                });
            });

            // Form validation placeholder
            const placeOrderBtn = document.querySelector('.btn-primary');
            if (placeOrderBtn) {
                placeOrderBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Basic form validation
                    const firstName = document.querySelector('input[placeholder="John"]');
                    const email = document.querySelector('input[placeholder="example@mail.com"]');
                    const phone = document.querySelector('input[placeholder="+20 123 456 7890"]');
                    const address = document.querySelector(
                        'input[placeholder="Street, Building, Apartment"]');

                    if (!firstName.value || !email.value || !phone.value || !address.value) {
                        alert('Please fill in all required fields');
                        return;
                    }

                    // Show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                    this.disabled = true;

                    // Simulate API call
                    setTimeout(() => {
                        alert('Order placed successfully!');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 1500);
                });
            }

            // Governorate and city interaction
            const governorateSelect = document.getElementById('governorate');
            const citySelect = document.getElementById('city');

            if (governorateSelect) {
                governorateSelect.addEventListener('change', function() {
                    // You can add dynamic city loading based on governorate here
                    console.log('Governorate selected:', this.value);
                });
            }

            // Adjust for mobile on load
            function adjustForMobile() {
                if (window.innerWidth <= 576) {
                    // إضافة padding للأجزاء التي تحتاجها
                    document.querySelectorAll('.checkout-page .col-12').forEach(col => {
                        col.style.paddingLeft = '15px';
                        col.style.paddingRight = '15px';
                    });
                }
            }

            // Call on load
            adjustForMobile();

            // Call on resize
            window.addEventListener('resize', adjustForMobile);
        });
        window.addEventListener('order_created', function(event) {
            toastr.success(event.detail)
        })
    </script>
</div>
