<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Rfq;
use App\Notifications\RfqNontification;
use Filament\Notifications\Notification;
use Livewire\Component;

class UserRfq extends Component
{
    public $name;
    public $phone;
    public $email;
    public $expected_price;
    public $description;

    public $product_id;
    public $product_variant_id;

    public $products = [];
    public $variants = [];
    public $items = [];
    public $rfq_expected_price;
    public $currencyId;


    public function rules()
    {
        // 🌟 التحقق من وجود أي منتجات مختارة في الجدول
        $hasProductItems = collect($this->items)->filter(function ($item) {
            return !empty($item['product_id']);
        })->isNotEmpty();

        $rules = [
            'name' => 'required|string|min:3|max:100',
            'phone' => ['required', 'phone:EG,SA,AE,QA,KW,BH,OM,JO,LB,MA,DZ,TN,LY,YE,IQ,SD,US'],

            'email' => 'required|email|max:150',
            // 🌟 التعديل: الوصف إجباري إذا لم يتم اختيار أي منتج
            'description' => $hasProductItems ? 'nullable|string|max:1000' : 'required|string|max:1000|min:10',

            // القاعدة الشرطية للسعر: إجباري إذا لم يتم اختيار أي منتج
            'rfq_expected_price' => $hasProductItems ? 'nullable|numeric|min:0' : 'required|numeric|min:0',

            'items.*.quantity' => 'required|integer|min:1',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'expected_price' => 'nullable|numeric|min:0',

        ];

        return $rules;
    }


    public function mount()
    {
        $this->items = [
            ['product_id' => '', 'product_variant_id' => '', 'expected_price' => '', 'quantity' => 1, 'variants' => []]
        ];
        $currencyCode=session('currency','EGP');
        $currency=Currency::whereCode($currencyCode)->first();
        $this->currencyId=$currency->id;

    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'product_variant_id' => '', 'expected_price' => '', 'quantity' => 1, 'variants' => []];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $name)
    {
        $parts = explode('.', $name);

        if (count($parts) === 2 && $parts[1] === 'product_id') {
            $index = $parts[0];
            $product = Product::with('variants')->find($value);

            $this->items[$index]['variants'] = $product
                ? ProductVariant::where('product_id', $product->id)
                ->with(['attributeValuesPivot.attribute', 'attributeValuesPivot.attributeValue'])
                ->get()
                : collect();

            $this->items[$index]['product_variant_id'] = '';

            if ($product) {
                if ($product->has_variants) {
                    // 🌟 التعديل: نترك السعر فارغًا إذا كان المنتج يحتوي على متغيرات (Variants)
                    $this->items[$index]['expected_price'] = '';
                } else {
                    // إذا لم يكن لديه متغيرات، نضع السعر الأساسي للمنتج.
                    $this->items[$index]['expected_price'] = $product->has_discount
                        ? $product->base_price * (1 - $product->discount_percentage / 100)
                        : $product->base_price;
                }
            } else {
                $this->items[$index]['expected_price'] = '';
            }
        }

        if (count($parts) === 2 && $parts[1] === 'product_variant_id') {
            $index = $parts[0];
            $variant = ProductVariant::find($value);

            if ($variant) {
                $product = $variant->product;
                $this->items[$index]['expected_price'] = $product->has_discount
                    ? $variant->price * (1 - $product->discount_percentage / 100)
                    : $variant->price;
            } else {
                // إذا تم مسح Variant
                $product = Product::find($this->items[$index]['product_id']);
                if ($product && !$product->has_variants) {
                    $this->items[$index]['expected_price'] = $product->has_discount
                        ? $product->base_price * (1 - $product->discount_percentage / 100)
                        : $product->base_price;
                } else {
                     $this->items[$index]['expected_price'] = '';
                }
            }
        }
    }

    public function submit()
    {
        // 1. تطبيق التحقق الأولي
        $this->validate();

        // 2. التحقق المخصص للمنتجات والفروع (Variants) والكميات
        $hasCustomErrors = false;
        foreach ($this->items as $index => $item) {
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    $this->addError("items.$index.product_id", 'المنتج غير موجود.');
                    $hasCustomErrors = true;
                    continue;
                }

                // الشرط الإضافي: إذا كان المنتج يتطلب Variant ولم يتم اختياره، فهو خطأ.
                if ($product->has_variants && empty($item['product_variant_id'])) {
                    $this->addError("items.$index.product_variant_id", 'الرجاء اختيار نوع المنتج (Variant) إجباري لهذا المنتج.');
                    $hasCustomErrors = true;
                }

                // التحقق من الكمية (يعمل بشكل صحيح)
                if (!empty($item['product_variant_id'])) {
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if ($variant && $item['quantity'] > $variant->quantity) {
                        $this->addError("items.$index.quantity", __('web.check_rfq_quantity'));
                        $hasCustomErrors = true;
                    }
                } else {
                    if ($item['quantity'] > $product->quantity) {
                        $this->addError("items.$index.quantity", __('web.check_rfq_quantity'));
                        $hasCustomErrors = true;
                    }
                }
            }
        }

        if ($this->getErrorBag()->isNotEmpty() || $hasCustomErrors) {
            // حل مشكلة عدم ظهور رسائل الأخطاء اليدوية
            $this->items = $this->items;
            return;
        }

        // 3. حفظ طلب عرض السعر (RFQ)
        $currency = Currency::where('code', session('currency', 'EGP'))->first();

        $rfq = Rfq::create([
            'name'          => $this->name,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'description'   => $this->description,
            'currency_id'   => $this->currencyId,
            'expected_price' => $this->rfq_expected_price ?: null,
        ]);

        // لو فيه منتجات أضفها
        foreach ($this->items as $item) {
            if (!empty($item['product_id'])) {
                $rfq->items()->create([
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?: null,
                    'quantity'           => $item['quantity'],
                    'expected_price'     => $item['expected_price'],
                ]);
            }
        }

        session()->flash('message', 'تم إرسال طلب عرض السعر بنجاح ✅');

        $this->reset(['name', 'phone', 'email', 'description', 'items', 'expected_price', 'rfq_expected_price']);
        $this->items = [
            ['product_id' => '', 'product_variant_id' => '', 'expected_price' => '', 'quantity' => 1, 'variants' => []]
        ];
        $admins=Admin::all();
        foreach($admins as $admin){
             if ($admin->hasRole('sales')) {
                $admin=Admin::where('email','sales@gmail.com')->first();
        $admin->notify(new RfqNontification($rfq));
        Notification::make()
                ->title('You Have a new RFQ from' . ' ' . $rfq->name)
                ->success()
                ->sendToDatabase($admin);
             }
        }


    }

    public function render()
    {
        $currencyCode = session('currency', 'EGP');

        $this->products = Product::whereHas('currency', function ($q) use ($currencyCode) {
            $q->where('code', $currencyCode);
        })->get();

        return view('livewire.user-rfq', [
            'products' => $this->products,
        ]);
    }
}
