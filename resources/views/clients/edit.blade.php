<!DOCTYPE html>
<html lang="en">
@include('partials.head')
<style>
    /* فرض اللون الأخضر لرسائل النجاح */
    #toast-container > div.toast-success {
        background-color: #35cd3a !important;
        opacity: 1 !important;
    }

    /* فرض اللون الأحمر لرسائل الخطأ */
    #toast-container > div.toast-error {
        background-color: #f3545d !important;
        opacity: 1 !important;
    }

    /* فرض اللون الأزرق لرسائل المعلومات */
    #toast-container > div.toast-info {
        background-color: #36a3f7 !important;
        opacity: 1 !important;
    }

    /* فرض اللون البرتقالي لرسائل التحذير */
    #toast-container > div.toast-warning {
        background-color: #ffa534 !important;
        opacity: 1 !important;
    }
</style>
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
                        <a href="../index.html" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" />
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
            </div>

            <div class="container">
                <div class="page-inner">

                    <div class="row">
                        <div class="col-lg-8 col-md-6 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Create Customer</div>
                                </div>
                                <div class="card-body">


                                    @livewire('update-client', ['client' => $client])




                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('partials.footer')
        </div>

        <!-- Custom template | don't include it in your project! -->
        @include('partials.custom-template')
        <!-- End Custom template -->
    </div>
    @include('partials.scripts')

</body>

</html>
