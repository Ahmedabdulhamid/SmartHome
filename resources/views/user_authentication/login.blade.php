<!DOCTYPE html>
<html lang="en">
@include('partials.head')

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        @include('partials.side_bar')
        <!-- End Sidebar -->
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="navbar brand"
                                class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                @include('partials.navBar')
                <!-- End Navbar -->
                <div class="container">
                    <div class="row d-flex justify-content-center align-items-center" style="min-height: 100vh;">
                        <div class="col-lg-6 col-md-6">

                            <div class="card p-4 shadow">
                                <h3 class="text-center mb-4">Login</h3>

                                <form action="{{ route('login.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="Enter your email" name="email" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" placeholder="Enter your password" name="password">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button class="btn btn-primary w-100 mb-3">Login</button>

                                    <div class="text-center">

                                        <a href="{{ route('register') }}">Create an Account</a>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>




        <!-- Custom template | don't include it in your project! -->
        @include('partials.custom-template')
        <!-- End Custom template -->
    </div>
     <!-- Custom template | don't include it in your project! -->
        @include('partials.footer')
        <!-- End Custom template -->
    @include('partials.scripts')
</body>

</html>
