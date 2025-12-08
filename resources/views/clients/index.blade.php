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

                    <div class="page-inner">
                        <a href="{{ route('clients.create') }}" class="btn btn-success my-5">Add New Client</a>
                        <table id="basic-datatables" class="display table table-striped table-hover my-5">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
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
    <script>

        $(document).ready(function() {
            $('#basic-datatables').DataTable({
                // تفعيل وضع Server-side Processing (المعالجة على جانب الخادم)
                processing: true,
                serverSide: true,
                responsive: true,
                // تحديد مصدر البيانات (الـ URL الذي يستدعي دالة getData() في المتحكم)
                // تأكد من أن هذا الـ URL صحيح في ملف web.php
                ajax: {
                    url: "{{ route('clients.data') }}", // استبدل بمسار الـ API الصحيح لدالة getData()
                    type: 'GET'
                },
                // تحديد أسماء الأعمدة وربطها بالبيانات الراجعة من Yajra
                columns: [
                    // تذكر: Yajra أضافت 'DT_RowIndex' باستخدام addIndexColumn()
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    }, // افترضنا وجود حقل phone في الجدول
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

           $(document).on('click', '.client-delete', function(e) {
                e.preventDefault();

                let clientId = $(this).attr('client_id');

                let url = "{{ route('clients.destroy', ':id') }}";
                url = url.replace(':id', clientId);
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن حذف هذه الفاتورة!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، قم بالحذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {

                        // 3. إرسال طلب Ajax بعد التأكيد
                        $.ajax({
                            url: url,
                            type: 'POST', // يجب استخدام POST في Ajax وتمرير _method=DELETE لـ Laravel
                            data: {
                                _token: '{{ csrf_token() }}', // رمز CSRF ضروري للحماية
                                _method: 'DELETE', // هذا يخبر Laravel بالتعامل مع الطلب كـ DELETE
                            },
                            success: function(response) {
                                Swal.fire(
                                    'تم الحذف!',
                                    'تم حذف الفاتورة بنجاح.',
                                    'success'
                                );

                                $('.client-badge').html(response.clientCount)
                                $('.invoice-badge').html(response.invoiceCount)

                                // تحديث جدول DataTables
                                $('#basic-datatables').DataTable().ajax.reload(null,
                                    false);
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'فشل!',
                                    'حدث خطأ أثناء محاولة الحذف.',
                                    'error'
                                );
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });





        });

    </script>


</body>

</html>
