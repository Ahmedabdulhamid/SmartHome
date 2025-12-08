<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
class DownloadPdf extends Component
{
    public $invoice;
    public function mount($invoice)
    {
        $this->invoice = Invoice::where('id', $invoice->id)->with('items')->first();
    }
 public function download()
    {
        // 1. التأكد من تحميل العلاقات اللازمة
        $this->invoice->load(['client', 'items']);

        // 2. توليد عرض HTML من Blade
        // تأكد أن ملف Blade الخاص بـ PDF هو 'invoices.pdf'
        $pdfContent = Pdf::loadView('invoices.invoice', [
            'invoice' => $this->invoice
        ]);

        $fileName = 'فاتورة-' . $this->invoice->invoice_number . '.pdf';

        // 3. إرجاع استجابة تحميل (Download Response) باستخدام Livewire
        // Livewire يفضل استخدام الدالة response() لتوليد الاستجابة مباشرة
        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent->output(); // إخراج محتوى الـ PDF
        }, $fileName);

        // إذا كان لديك إصدار Livewire قديم (v2)، قد تحتاج إلى:
        /*
        return Response::streamDownload(function () use ($pdfContent) {
             echo $pdfContent->output();
        }, $fileName);
        */
    }
    public function render()
    {
        return view('livewire.invoices.download-pdf');
    }
}
