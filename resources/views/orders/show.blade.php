<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', 'تفاصيل الطلب #' . $order->id)
@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">
        <div class="container py-5" data-aos="fade-up" data-aos-delay="100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bolder mb-0">تفاصيل الطلب #{{ $order->id }}</h3>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    رجوع للوحة الحساب
                </a>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted">الحالة</div>
                            <div class="fw-bold">{{ $order->status }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">التاريخ</div>
                            <div class="fw-bold">{{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">الإجمالي</div>
                            <div class="fw-bold">{{ number_format((float) $order->total_amount, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">عدد المنتجات</div>
                            <div class="fw-bold">{{ $order->items->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">منتجات الطلب</h5>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @php
                                                $name = is_string($item->product_name) ? json_decode($item->product_name, true) : $item->product_name;
                                            @endphp
                                            {{ is_array($name) ? ($name[app()->getLocale()] ?? reset($name)) : ($item->product?->name ?? 'Product') }}
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format((float) $item->price, 2) }}</td>
                                        <td>{{ number_format((float) $item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد عناصر في هذا الطلب.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <div id="preloader"></div>

    @include('users_layout.script')
    @livewireScripts
</body>

</html>
