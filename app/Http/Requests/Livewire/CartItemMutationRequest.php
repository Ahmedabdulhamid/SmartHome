<?php

namespace App\Http\Requests\Livewire;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CartItemMutationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $variantId = $this->input('product_variant_id');

            if (! $variantId) {
                return;
            }

            $variant = ProductVariant::query()->find($variantId);

            if ($variant && (int) $variant->product_id !== (int) $this->input('product_id')) {
                $validator->errors()->add('product_variant_id', __('validation.exists', [
                    'attribute' => 'product variant',
                ]));
            }
        });
    }
}
