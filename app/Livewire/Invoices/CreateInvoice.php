<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Client;
use Livewire\Attributes\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // لإدارة المعاملات (Transactions)

class CreateInvoice extends Component
{
    // ... (خصائص الفاتورة الرئيسية: client_id, invoice_date, due_date)
    // لا نحتاج لـ total_amount هنا لأنها تُحسب آلياً

    #[Rule('required|exists:clients,id')]
    public $client_id = '';

    #[Rule('required|date|after_or_equal:today')]
    public $invoice_date;

    #[Rule('required|date|after_or_equal:invoice_date')]
    public $due_date;

    // 🚀 الخصائص الجديدة لـ Invoice Items 🚀
    #[Rule(['items' => 'array', 'items.*.description' => 'required|string|max:255', 'items.*.quantity' => 'required|integer|min:1', 'items.*.price' => 'required|numeric|min:0.01'])]
    public $items = []; // مصفوفة لتخزين بنود الفاتورة

    public $successMessage = '';

    public function mount()
    {
        $this->invoice_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        // إضافة بند افتراضي واحد عند التحميل
        $this->addItem();
    }

    // دالة لإضافة بند جديد إلى المصفوفة
    public function addItem()
    {
        $this->items[] = [
            'description' => '',
            'quantity' => 1,
            'price' => 0.00,
            'total' => 0.00,
        ];
    }

    // دالة لإزالة بند من المصفوفة باستخدام الـ index
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // إعادة ترتيب المفاتيح
    }

    // دالة حاسبة لحساب الإجمالي
    public function getTotalAmountProperty()
    {
        $total = 0;
        foreach ($this->items as $index => $item) {
            $subtotal = (float)$item['quantity'] * (float)$item['price'];
            $this->items[$index]['total'] = number_format($subtotal, 2, '.', ''); // تحديث الإجمالي الفرعي
            $total += $subtotal;
        }
        return number_format($total, 2, '.', '');
    }

    /**
     * دالة الحفظ: يتم استدعاؤها عند إرسال النموذج.
     */
    public function saveInvoice()
    {
        // 1. التحقق من صحة البيانات
        $this->validate();

        // 2. توليد رقم الفاتورة العشوائي
        $uniqueInvoiceNumber = $this->generateUniqueInvoiceNumber();

        // 3. إدارة المعاملات لضمان حفظ الفاتورة والبنود بنجاح
        DB::transaction(function () use ($uniqueInvoiceNumber) {

            // إنشاء الفاتورة الرئيسية
            $invoice = Invoice::create([
                'user_id' => auth()->guard('web')->id(),
                'client_id' => $this->client_id,
                'invoice_number' => $uniqueInvoiceNumber,
                'invoice_date' => $this->invoice_date,
                'due_date' => $this->due_date,
                'total_amount' => $this->total_amount, // يتم الحصول عليها من الـ accessor
            ]);

            // إنشاء بنود الفاتورة (Invoice Items)
            $invoiceItems = [];
            foreach ($this->items as $item) {
                $invoiceItems[] = new \App\Models\InvoiceItem([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            // ربط وحفظ البنود (Livewire يقوم بتحويل $item['total'] إلى نص؛ يجب تحويله إلى رقم في قاعدة البيانات)
            $invoice->items()->saveMany($invoiceItems);
        }); // نهاية المعاملة

        // 4. إطلاق حدث Livewire
        $this->successMessage = 'تم إنشاء الفاتورة رقم ' . $uniqueInvoiceNumber . ' بنجاح.';
        $this->dispatch('invoice-created', ['message' => $this->successMessage]);

        $this->dispatch('updateInvoiceCount');
        $this->reset(['client_id', 'items']);
        $this->mount(); // لإعادة إضافة بند افتراضي
    }

    // ... (دالة generateUniqueInvoiceNumber() و render() كما هي)

    private function generateUniqueInvoiceNumber()
    {
        do {
            $number = 'INV-' . Str::upper(Str::random(8));
        } while (Invoice::where('invoice_number', $number)->exists());

        return $number;
    }

    public function render()
    {
        $clients = Client::all(['id', 'name']);

        return view('livewire.invoices.create-invoice', [
            'clients' => $clients,
        ]);
    }
}
