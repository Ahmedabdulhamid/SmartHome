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
        $hasProductItems = collect($this->items)
            ->filter(fn ($item) => !empty($item['product_id']))
            ->isNotEmpty();

        return [
            'name' => 'required|string|min:3|max:100',
            'phone' => ['required', 'phone:EG,SA,AE,QA,KW,BH,OM,JO,LB,MA,DZ,TN,LY,YE,IQ,SD,US'],
            'email' => 'required|email|max:150',
            'description' => $hasProductItems ? 'nullable|string|max:1000' : 'required|string|max:1000|min:10',
            'rfq_expected_price' => $hasProductItems ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'expected_price' => 'nullable|numeric|min:0',
        ];
    }

    public function mount()
    {
        $this->items = [
            ['product_id' => '', 'product_variant_id' => '', 'expected_price' => '', 'quantity' => 1, 'variants' => []],
        ];

        $currencyCode = session('currency', 'EGP');
        $currency = Currency::whereCode($currencyCode)->first();
        $this->currencyId = $currency?->id;
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

    public function updated($property, $value)
    {
        if (!str_starts_with($property, 'items.')) {
            return;
        }

        $parts = explode('.', $property);

        if (count($parts) !== 3) {
            return;
        }

        [, $index, $field] = $parts;

        if ($field === 'product_id') {
            $this->syncProductSelection((int) $index, $value);
            return;
        }

        if ($field === 'product_variant_id') {
            $this->syncVariantSelection((int) $index, $value);
        }
    }

    protected function syncProductSelection(int $index, $productId): void
    {
        $product = Product::with('variants')->find($productId);

        $this->items[$index]['variants'] = $product
            ? ProductVariant::where('product_id', $product->id)
                ->with(['attributeValuesPivot.attribute', 'attributeValuesPivot.attributeValue'])
                ->get()
                ->all()
            : [];

        $this->items[$index]['product_variant_id'] = '';
        $this->items[$index]['expected_price'] = $product && !$product->has_variants
            ? $product->actual_price
            : '';
    }

    protected function syncVariantSelection(int $index, $variantId): void
    {
        $variant = ProductVariant::with('product')->find($variantId);

        if ($variant) {
            $this->items[$index]['expected_price'] = $variant->actual_price;
            return;
        }

        $product = Product::find($this->items[$index]['product_id']);
        $this->items[$index]['expected_price'] = $product && !$product->has_variants
            ? $product->actual_price
            : '';
    }

    public function submit()
    {
        $this->validate();

        $hasCustomErrors = false;
        foreach ($this->items as $index => $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $product = Product::find($item['product_id']);

            if (!$product) {
                $this->addError("items.$index.product_id", 'المنتج غير موجود.');
                $hasCustomErrors = true;
                continue;
            }

            if ($product->has_variants && empty($item['product_variant_id'])) {
                $this->addError("items.$index.product_variant_id", 'الرجاء اختيار نوع المنتج (Variant) إجباري لهذا المنتج.');
                $hasCustomErrors = true;
            }

            if (!empty($item['product_variant_id'])) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant && $item['quantity'] > $variant->quantity) {
                    $this->addError("items.$index.quantity", __('web.check_rfq_quantity'));
                    $hasCustomErrors = true;
                }
            } elseif ($item['quantity'] > $product->quantity) {
                $this->addError("items.$index.quantity", __('web.check_rfq_quantity'));
                $hasCustomErrors = true;
            }
        }

        if ($this->getErrorBag()->isNotEmpty() || $hasCustomErrors) {
            $this->items = $this->items;
            return;
        }

        $rfq = Rfq::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'currency_id' => $this->currencyId,
            'expected_price' => $this->rfq_expected_price ?: null,
        ]);

        foreach ($this->items as $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $rfq->items()->create([
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?: null,
                'quantity' => $item['quantity'],
                'expected_price' => $item['expected_price'],
            ]);
        }

        session()->flash('message', 'تم إرسال طلب عرض السعر بنجاح ✅');

        $this->reset(['name', 'phone', 'email', 'description', 'items', 'expected_price', 'rfq_expected_price']);
        $this->items = [
            ['product_id' => '', 'product_variant_id' => '', 'expected_price' => '', 'quantity' => 1, 'variants' => []],
        ];

        $admins = Admin::all();
        foreach ($admins as $admin) {
            if (!$admin->hasRole(['sales', 'Super Admin'])) {
                continue;
            }

            $admin->notify(new RfqNontification($rfq));
            Notification::make()
                ->title('You Have a new RFQ from ' . $rfq->name)
                ->success()
                ->sendToDatabase($admin);
        }
    }

    public function render()
    {
        $currencyCode = session('currency', 'EGP');

        $this->products = Product::whereHas('currency', function ($query) use ($currencyCode) {
            $query->where('code', $currencyCode);
        })->get();

        return view('livewire.user-rfq', [
            'products' => $this->products,
        ]);
    }
}
