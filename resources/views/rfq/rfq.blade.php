<!DOCTYPE html>
<html lang="en">
@section('title',__('web.rfq'))
@include('users_layout.head')
<style>
    .rfq-container {
    max-width: 800px;
    margin: 30px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 12px;
    background: #f9f9f9;
    font-family: "Segoe UI", sans-serif;
}

.alert-success {
    margin-bottom: 15px;
    padding: 12px;
    background: #d4edda;
    color: #155724;
    border-radius: 6px;
    font-weight: 500;
}

.form-group {
    margin-bottom: 18px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #aaa;
    border-radius: 6px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 4px rgba(37, 99, 235, 0.3);
    outline: none;
}

.section-title {
    font-weight: bold;
    margin: 20px 0 10px;
    font-size: 18px;
    color: #111827;
}

.product-card {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.hint {
    display: block;
    color: #6b7280;
    font-size: 13px;
    margin-top: 5px;
}

.error-message {
    color: #dc2626;
    font-size: 14px;
    margin-top: 4px;
    display: block;
}

.btn {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: background 0.2s ease, transform 0.1s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: #1d4ed8;
    color: white;
}

.btn-success {
    background: #059669;
    color: white;
    margin-top: 10px;
}

.btn-danger {
    background: #dc2626;
    color: white;
    margin-top: 10px;
}

.form-submit {
    text-align: center;
    margin-top: 20px;
}

</style>
@livewireStyles

<body class="index-page" style="@if (app()->getLocale()=='ar')
    direction:rtl
    @else
    direction:ltr
@endif">
    @include('users_layout.header')

    <main class="main"data-aos="fade-up" data-aos-delay="100">


        @livewire('user-rfq')

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
