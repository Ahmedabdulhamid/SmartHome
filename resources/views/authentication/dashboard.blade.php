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
<main class="main">
    <div class="container py-5" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-5 justify-content-center">

            <div class="col-lg-4 col-md-5">
                <div class="card shadow-lg border-0 h-100 text-center profile-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="profile-img-container mb-4">
                            <img src="path/to/user/avatar.jpg" alt="صورة المستخدم" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--bs-primary);">
                        </div>

                        <h4 class="card-title fw-bold mb-1">{{ Auth::user()->name ?? 'اسم المستخدم' }}</h4>

                        <p class="text-muted">{{ Auth::user()->email ?? 'user@example.com' }}</p>

                        <hr>

                        <button class="btn btn-primary w-100 fw-bold shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil-square me-2"></i> تعديل حسابي
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-7">
                <div class="card shadow-sm border-0 h-100 content-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bolder mb-4 text-primary">👋 مرحباً بك!</h3>
                        <p class="text-muted">هنا يمكنك الاطلاع على تفاصيل حسابك وإدارتها بسهولة.</p>

                        <div class="mt-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">بيانات الحساب</h5>

                            <div class="row g-3 info-row">
                                <div class="col-sm-6">
                                    <p class="text-muted mb-0">الاسم الكامل:</p>
                                    <h6 class="fw-bold">{{ Auth::user()->full_name ?? 'الاسم غير محدد' }}</h6>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted mb-0">رقم الهاتف:</p>
                                    <h6 class="fw-bold">{{ Auth::user()->phone ?? 'غير متوفر' }}</h6>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted mb-0">تاريخ التسجيل:</p>
                                    <h6 class="fw-bold">{{ Auth::user()->created_at->format('Y-m-d') ?? 'N/A' }}</h6>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted mb-0">المدينة:</p>
                                    <h6 class="fw-bold">{{ Auth::user()->city ?? 'غير محددة' }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top">
                            <h5 class="fw-bold mb-3">إعدادات إضافية</h5>
                            <a href="#" class="btn btn-outline-secondary me-2"><i class="bi bi-key me-1"></i> تغيير كلمة المرور</a>
                            <a href="#" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> حذف الحساب</a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</main>


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
