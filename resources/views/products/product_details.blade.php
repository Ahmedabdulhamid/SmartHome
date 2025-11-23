<!DOCTYPE html>
<html lang="en">

@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale()=='ar')
    direction:rtl
    @else
    direction:ltr
@endif">
    @include('users_layout.header')

    <main class="main">


     @livewire('prduct-details', ['product' => $product])

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
