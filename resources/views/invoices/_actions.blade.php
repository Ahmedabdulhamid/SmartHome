<div class="d-flex">
    @if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin')
        <a class="btn btn-sm btn-info mx-1 edit-invoice" href="{{ route('invoices.edit', $invoice->id) }}">
            Edit
        </a>
    @endif


    {{-- مُعرف الفاتورة ضروري لعمل JavaScript في الصفحة الرئيسية --}}
    @if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin')
        <button class="btn btn-sm btn-danger mx-1 delete" invoice-id="{{ $invoice->id }}">Delete</button>
    @endif
    @livewire('invoices.download-pdf', ['invoice' => $invoice])

</div>
