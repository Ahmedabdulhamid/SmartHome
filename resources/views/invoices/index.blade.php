<!DOCTYPE html>
<html lang="en">
@include('partials.head')

<body>
    <div class="wrapper">
        @include('partials.side_bar')
        <div class="main-panel">
            @include('partials.navBar')

            <div class="container">
                <div class="page-inner">
                    <a href="{{ route('invoices.create') }}" class="btn btn-success">Add
                        New Invoice </a>

                    {{-- 🛑 Modal Structure (تم وضعه هنا لسهولة التجميع) --}}
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">إنشاء فاتورة جديدة</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{-- 🚀 استدعاء مكون Livewire --}}
                                    @livewire('invoices.create-invoice')
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- 🛑 End Modal Structure --}}


                    <table id="invoices-table" class="display table table-striped table-hover my-5">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice Number</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Total Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            @include('partials.footer')
        </div>
        @include('partials.custom-template')
    </div>

    @include('partials.scripts')

    {{-- 🚀 كود JavaScript الرئيسي لـ DataTables وربط الأحداث 🚀 --}}
    <script>
        // التأكد من تحميل jQuery بالكامل
        $(document).ready(function() {

            // 1. تهيئة DataTables
            $('#invoices-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('invoices.data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // 2. تفويض الأحداث لزر الحذف (.delete)
            // هذا يضمن أن الحدث يعمل على الأزرار المضافة بواسطة DataTables (Ajax)
            $(document).on('click', '.delete', function(e) {
                e.preventDefault();


                let invoiceId = $(this).attr('invoice-id');



                let url = "{{ route('invoices.destroy', ':id') }}";
                url = url.replace(':id', invoiceId);

                // 2. استخدام Swal.fire للتأكيد
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
                                $('.invoice-badge').html(response.invoiceCount)
                                // تحديث جدول DataTables
                                $('#invoices-table').DataTable().ajax.reload(null,
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

            // 3. معالجة حدث Livewire (إغلاق Modal وتحديث الجدول)
            window.addEventListener('invoice-created', (event) => {

                $('#invoices-table').DataTable().ajax.reload(null, false);

                const modalElement = document.getElementById('exampleModal');
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrap.Modal(modalElement);
                }
                modal.hide();

                // الحل لضمان اختفاء الظل (Backdrop)
                setTimeout(() => {
                    document.querySelector('.modal-backdrop')?.remove();
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    // يمكن عرض رسالة النجاح هنا للمستخدم
                    alert(event.detail.message);
                }, 300);

            });
        });
    </script>
</body>

</html>
