<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Client;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;

class EditInvoice extends Component
{
    #[Locked]
    public $invoice_id; // لتخزين ID الفاتورة التي يتم تعديلها

    // الخصائص الرئيسية
    #[Rule('required|exists:clients,id')]
    public $client_id = '';

    public $invoice_number;

    #[Rule('required|date')]
    public $invoice_date;

    #[Rule('required|date|after_or_equal:invoice_date')]
    public $due_date;

    // خصائص بنود الفاتورة (Items)
    #[Rule(['items' => 'array', 'items.*.description' => 'required|string|max:255', 'items.*.quantity' => 'required|integer|min:1', 'items.*.price' => 'required|numeric|min:0.01'])]
    public $items = [];

    public $successMessage = '';

    /**
     * دالة mount: يتم استدعاؤها مرة واحدة عند تحميل المكون.
     * تستقبل كائن الفاتورة (Invoice) عبر Model Binding.
     */
    public function mount(Invoice $invoice)
    {
        // 1. تحميل بيانات الفاتورة الرئيسية
        $this->invoice_id = $invoice->id;
        $this->client_id = $invoice->client_id;
        $this->invoice_number = $invoice->invoice_number;

        // استخدام format() لضمان عرض التاريخ بشكل صحيح في حقل <input type="date">
        $this->invoice_date = $invoice->invoice_dat;
        $this->due_date = $invoice->due_dat;

        // 2. تحميل بنود الفاتورة
        $this->items = [];
        foreach ($invoice->items as $item) {
            $this->items[] = [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'price' => $item->price,
                // التأكد من تنسيق الإجمالي الفرعي
                'total' => number_format($item->total, 2, '.', ''),
            ];
        }

        // إضافة بند افتراضي إذا كانت الفاتورة بلا بنود
        if (empty($this->items)) {
            $this->addItem();
        }

        $this->successMessage = '';
    }

    // دالة لحساب الإجمالي الكلي (Computed Property)
    public function getTotalAmountProperty()
    {
        $total = 0;
        foreach ($this->items as $index => $item) {
            $subtotal = (float)$item['quantity'] * (float)$item['price'];
            $this->items[$index]['total'] = number_format($subtotal, 2, '.', '');
            $total += $subtotal;
        }
        return number_format($total, 2, '.', '');
    }

    // دوال إدارة البنود
    public function addItem()
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'price' => 0.00, 'total' => 0.00,];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updateInvoice()
    {
        // 1. التحقق من صحة البيانات
        $this->validate([
            // استخدام $this->invoice_id لاستثناء الفاتورة الحالية من فحص التكرار
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $this->invoice_id,
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
        ]);

        if (empty($this->items)) {
            $this->successMessage = 'يجب أن تحتوي الفاتورة على بند واحد على الأقل.';
            return;
        }

        DB::transaction(function () {

            $invoice = Invoice::findOrFail($this->invoice_id);

            // 2. تحديث الفاتورة الرئيسية
            $invoice->update([
                'client_id' => $this->client_id,
                'invoice_number' => $this->invoice_number,
                'invoice_date' => $this->invoice_date,
                'due_date' => $this->due_date,
                'total_amount' => $this->totalAmount, // استخدام القيمة المحسوبة
            ]);

            // 3. حذف البنود القديمة وحفظ البنود الجديدة
            $invoice->items()->delete();

            $newItems = [];
            foreach ($this->items as $item) {
                $newItems[] = new \App\Models\InvoiceItem([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }
            $invoice->items()->saveMany($newItems);
        });

        $this->successMessage = 'تم تحديث الفاتورة رقم ' . $this->invoice_number . ' بنجاح.';

        // 4. إطلاق حدث للتنبيه أو إعادة التوجيه (يمكن تعديله حسب الحاجة)
        $this->dispatch('invoice-updated', ['message' => $this->successMessage]);
    }

    public function render()
    {
        $clients = Client::all(['id', 'name']);

        return view('livewire.invoices.edit-invoice', [
            'clients' => $clients,
        ]);
    }
}
