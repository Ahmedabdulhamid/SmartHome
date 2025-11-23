<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class PrductDetails extends Component
{
    public $product;
    public $quantity = 1;
    public $relatedProducts;
    public $selectedVariant;
    public $cartCount;
    public function mount($product)
    {
        $this->product = $product;

        $category = $this->product->category;
        $currencyCode = session('currency', 'EGP');


        // ✅ التعديل 1: الاستعلام المباشر (تجنب N+1 والـ first())
        $this->relatedProducts = Product::query()

            // 1. ربط المنتجات بالكيان (الفئة)
            ->whereBelongsTo($category)

            // 2. استثناء المنتج الحالي
            ->where('id', '!=', $this->product->id)

            // ✅ الشرط 3: يجب أن يكون المنتج نشطًا
            ->where('status', 'active')

            // ✅ الشرط 4: منطق التوفر الشامل
            ->where(function ($query) {

                // (أ) المنتجات البسيطة
                $query->where('has_variants', false)
                    ->where(function ($q) {
                        $q->where('manage_quantity', false)
                            ->orWhere(function ($q2) {
                                $q2->where('manage_quantity', true)
                                    ->where('quantity', '>', 0);
                            });
                    })

                    // OR

                    // (ب) المنتجات المركبة (Variants)
                    ->orWhere(function ($q) {
                        $q->where('has_variants', true)
                            // يجب أن يكون لديها متغير واحد على الأقل متوفر
                            ->whereHas('variants', function ($variantQuery) {
                                $variantQuery->where(function ($v) {
                                    $v->where('manage_quantity', false)
                                        ->orWhere(function ($v2) {
                                            $v2->where('manage_quantity', true)
                                                ->where('quantity', '>', 0);
                                        });
                                });
                            });
                    });
            })

            // ✅ الشرط 5: تصفية العملة باستخدام whereHas مباشرة
            ->whereHas('currency', function ($q) use ($currencyCode) {
                $q->where('code', $currencyCode);
            })

            // ✅ الشرط 6: Eager Loading للصور الضرورية
            ->with(['images', 'currency'])

            ->limit(4) // 💡 اقتراح: عادةً ما تكون المنتجات ذات الصلة محدودة بعدد قليل
            ->get();
    }
    public function incrementQuantity()
    {
        $this->quantity++;
    }
    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
   public function addToCart($id)
{
    // 1️⃣ الحصول على المنتج
    $product = Product::with(['images', 'currency', 'variants'])->find($id);

    if (!$product) {
        return $this->dispatch('error_cart_not_found', message: __('web.error_cart_not_found'));
    }

    // 2️⃣ التحقق من الكمية المطلوبة
    if ($this->quantity < 1) {
        return $this->dispatch('error_cart_quantity', message: __('web.error_cart_invalid_quantity'));
    }

    // 3️⃣ إنشاء أو جلب السلة
    $cart = Cart::firstOrCreate([
        'user_id' => auth()->guard('web')->user()->id ?? null,
        'session_id' => session()->getId(),
    ]);

    // 4️⃣ المنتج بدون variants
    if (!$product->has_variants) {
        if ($this->quantity > $product->quantity) {
            return $this->dispatch('error_cart_quantity', message: __('web.error_cart_quantity'));
        }

        $existingItem = $cart->items()->where('product_id', $product->id)
            ->wherePivotNull('product_variant_id')
            ->first();

        if ($existingItem) {
            $cart->items()->updateExistingPivot($product->id, [
                'quantity' => $existingItem->pivot->quantity + $this->quantity,
                'product_variant_id' => null,
            ]);
        } else {
            $cart->items()->attach($product->id, [
                'quantity' => $this->quantity,
                'product_variant_id' => null,
            ]);
        }

        return $this->dispatch('cart_updated', message: __('web.success_add_cart'));
    }

    // 5️⃣ المنتج له variants
    if (!$this->selectedVariant) {
        // المستخدم لم يختر variant بعد
        return $this->dispatch('error_cart_variants', message: __('web.error_cart_variants'));
    }

    // التحقق من كمية المتغير المتاحة
    if ($this->quantity > $this->selectedVariant->quantity) {
        return $this->dispatch('error_cart_quantity', message: __('web.error_cart_quantity'));
    }

    // التحقق من وجود نفس المتغير في السلة
    $existingItem = $cart->items()
        ->where('product_id', $product->id)
        ->wherePivot('product_variant_id', $this->selectedVariant->id)
        ->first();

    if ($existingItem) {
        $cart->items()->updateExistingPivot($product->id, [
            'quantity' => $existingItem->pivot->quantity + $this->quantity,
            'product_variant_id' => $this->selectedVariant->id,
        ]);
    } else {
        $cart->items()->attach($product->id, [
            'quantity' => $this->quantity,
            'product_variant_id' => $this->selectedVariant->id,
        ]);
    }

    $this->cartCount = $cart->items()->count();
    $this->dispatch('cart_count_updated', count: $this->cartCount);
    return $this->dispatch('cart_updated', message: __('web.success_add_cart'));
}

    public function selectVariant($variantId)
    {
        $this->selectedVariant = ProductVariant::with(['product'])->find($variantId);
    }

    public function render()
    {
        return view('livewire.prduct-details');
    }
}
