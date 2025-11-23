<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@section('title', __('web.profile'))
@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

    <main class="main">

        {{-- ✅ عرض التنبيهات باستخدام Flasher --}}
        @if (session('profile_success'))
            <div class="alert alert-success alert-dismissible fade show mt-3 mx-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('profile_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3 mx-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center position-fixed top-0 start-50 translate-middle-x mt-3"
                style="z-index: 9999; min-width: 300px;" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show text-center position-fixed top-0 start-50 translate-middle-x mt-3"
                style="z-index: 9999; min-width: 300px;" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="container py-5" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-5">

                {{-- ✅ كارت البروفايل --}}
                <div class="col-lg-4 col-md-5">
                    <div class="card shadow-lg border-0 h-100 text-center profile-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="profile-img-container mb-4">
                                <img src="{{ asset('assets/img/تنزيل.png') }}" alt="{{ __('web.user_image') }}"
                                    class="img-fluid rounded-circle"
                                    style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--bs-primary);">
                            </div>

                            <h4 class="card-title fw-bold mb-1">
                                {{ Auth::guard('web')->user()->name ?? __('web.username') }}
                            </h4>
                            <p class="text-muted">{{ Auth::guard('web')->user()->email ?? 'user@example.com' }}</p>

                            <hr>

                            <button class="btn btn-primary w-100 fw-bold shadow-sm py-2" data-bs-toggle="modal"
                                data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square me-2"></i> {{ __('web.edit_profile') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ✅ كارت البيانات --}}
                <div class="col-lg-7 col-md-7">
                    <div class="card shadow-sm border-0 h-100 content-card">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="fw-bolder mb-4 text-primary">{{ __('web.welcome_message') }}</h3>
                            <p class="text-muted">{{ __('web.account_description') }}</p>

                            <div class="mt-4">
                                <h5 class="fw-bold mb-3 border-bottom pb-2">{{ __('web.account_info') }}</h5>

                                <div class="row g-3 info-row">
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-0">{{ __('web.full_name') }}</p>
                                        <h6 class="fw-bold">
                                            {{ Auth::guard('web')->user()?->name ?? __('web.not_defined') }}
                                        </h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-0">{{ __('web.email') }}</p>
                                        <h6 class="fw-bold">
                                            {{ Auth::guard('web')->user()?->email ?? __('web.not_available') }}
                                        </h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-0">{{ __('web.registered_at') }}</p>
                                        <h6 class="fw-bold">
                                            {{ optional(Auth::guard('web')->user()?->created_at)->format('Y-m-d') ?? 'N/A' }}
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-3 border-top">
                                <h5 class="fw-bold mb-3">{{ __('web.additional_settings') }}</h5>

                                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">
                                    <i class="bi bi-key me-1"></i> {{ __('web.change_password') }}
                                </button>

                                <form action="{{ route('profile.destroy') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> {{ __('web.delete_account') }}
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    @include('users_layout.footer')

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <div id="preloader"></div>

    {{-- ✅ مودال تعديل الحساب --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="editProfileModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> {{ __('web.edit_profile_title') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_name"
                                    class="form-label fw-semibold">{{ __('web.full_name') }}</label>
                                <input type="text" class="form-control" id="edit_name"
                                    value="{{ Auth::guard('web')->user()->name }}" name="name">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="edit_email" class="form-label fw-semibold">{{ __('web.email') }}</label>
                                <input type="email" class="form-control" id="edit_email"
                                    value="{{ Auth::guard('web')->user()->email }}" disabled>
                                <div class="form-text">{{ __('web.email_not_editable') }}</div>
                            </div>

                            <div class="col-12 mt-4">
                                <label for="edit_profile_picture"
                                    class="form-label fw-semibold">{{ __('web.change_profile_picture') }}</label>
                                <input type="file" class="form-control" id="edit_profile_picture"
                                    name="profile_picture">
                                @error('profile_picture')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                {{ __('web.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">{{ __('web.save_changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ مودال تغيير كلمة المرور --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title fw-bold" id="changePasswordModalLabel">
                        <i class="bi bi-key me-2"></i> {{ __('web.change_password_title') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('password.change') }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if (!Auth::guard('web')->user()?->social_id)
                            <div class="mb-3">
                                <label for="current_password"
                                    class="form-label fw-semibold">{{ __('web.current_password') }}</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password">
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                {{ __('web.set_password_first_time_hint') }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="new_password"
                                class="form-label fw-semibold">{{ __('web.new_password') }}</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <div class="form-text">{{ __('web.password_hint') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation"
                                class="form-label fw-semibold">{{ __('web.confirm_new_password') }}</label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation">
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                {{ __('web.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">{{ __('web.save_changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('users_layout.script')

    {{-- ✅ عرض تنبيهات Flasher (الأفضل هنا قرب نهاية الصفحة) --}}


    @livewireScripts
    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(a => {
                a.classList.remove('show');
                setTimeout(() => a.remove(), 500);
            });
        }, 3000);
    </script>
</body>

</html>
