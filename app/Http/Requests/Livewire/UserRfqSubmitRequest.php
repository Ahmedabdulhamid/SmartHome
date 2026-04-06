<?php

namespace App\Http\Requests\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserRfqSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $items = collect($this->input('items', []));
        $hasProductItems = $items->contains(fn (array $item): bool => ! empty($item['product_id']));

        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'phone' => ['required', 'phone:EG,SA,AE,QA,KW,BH,OM,JO,LB,MA,DZ,TN,LY,YE,IQ,SD,US'],
            'email' => ['required', 'email', 'max:150'],
            'description' => [$hasProductItems ? 'nullable' : 'required', 'string', 'min:10', 'max:1000'],
            'rfq_expected_price' => [$hasProductItems ? 'nullable' : 'required', 'numeric', 'min:0'],
            'items' => ['array'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.expected_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('items', []) as $index => $item) {
                $productId = $item['product_id'] ?? null;

                if (! $productId) {
                    continue;
                }

                $product = Product::query()->find($productId);

                if (! $product) {
                    $validator->errors()->add("items.$index.product_id", 'المنتج غير موجود.');
                    continue;
                }

                $variantId = $item['product_variant_id'] ?? null;

                if ($product->has_variants && ! $variantId) {
                    $validator->errors()->add("items.$index.product_variant_id", 'الرجاء اختيار نوع المنتج (Variant) إجباري لهذا المنتج.');
                    continue;
                }

                $quantity = (int) ($item['quantity'] ?? 0);

                if ($variantId) {
                    $variant = ProductVariant::query()->find($variantId);

                    if (! $variant || (int) $variant->product_id !== (int) $product->id) {
                        $validator->errors()->add("items.$index.product_variant_id", 'النوع المختار لا يتبع هذا المنتج.');
                        continue;
                    }

                    if ($quantity > (int) $variant->quantity) {
                        $validator->errors()->add("items.$index.quantity", __('web.check_rfq_quantity'));
                    }

                    continue;
                }

                if ($quantity > (int) $product->quantity) {
                    $validator->errors()->add("items.$index.quantity", __('web.check_rfq_quantity'));
                }
            }
        });
    }
}
