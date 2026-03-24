<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class CartPage extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $total = 0;

    protected $listeners = ['cartUpdated' => 'getCartItems'];

    public function mount()
    {
        $this->getCartItems();
    }

    public function getCartItems()
    {
        $cart = $this->getCurrentCart();

        if ($cart) {
            $this->cartItems = $cart->items()
                ->with(['currency', 'images'])
                ->get();

            $this->calculateTotals();
        } else {
            $this->cartItems = [];
            $this->subtotal = 0;
            $this->total = 0;
        }
    }

    public function increaseQty($productId, $variantId = null)
    {
        $cart = $this->getCurrentCart();
        if (!$cart) return;

        $existingItem = $this->findCartItemThroughRelationship($cart, $productId, $variantId);

        if ($existingItem) {
            $newQuantity = $existingItem->pivot->quantity + 1;
            $unitPrice = $this->getUnitPrice($productId, $variantId);
            $newPrice = $newQuantity * $unitPrice;

            // ✅ التصحيح: استخدام wherePivot مع updateExistingPivot
            $query = $cart->items();

            if ($variantId && $variantId !== 'null') {
                $query->wherePivot('product_variant_id', $variantId);
            } else {
                $query->wherePivot('product_variant_id', null);
            }

            $query->updateExistingPivot($productId, [
                'quantity' => $newQuantity,
                'price' => $newPrice
            ]);
        } else {
            $unitPrice = $this->getUnitPrice($productId, $variantId);

            $cart->items()->attach($productId, [
                'product_variant_id' => $variantId && $variantId !== 'null' ? $variantId : null,
                'quantity' => 1,
                'price' => $unitPrice
            ]);
        }

        $this->getCartItems();
        $this->dispatch('cartUpdated');
    }

    public function decreaseQty($productId, $variantId = null)
    {
        $cart = $this->getCurrentCart();
        if (!$cart) return;

        $existingItem = $this->findCartItemThroughRelationship($cart, $productId, $variantId);
        if (!$existingItem) return;

        $currentQuantity = $existingItem->pivot->quantity;

        if ($currentQuantity > 1) {
            $newQuantity = $currentQuantity - 1;
            $unitPrice = $this->getUnitPrice($productId, $variantId);
            $newPrice = $newQuantity * $unitPrice;

            // ✅ التصحيح: استخدام wherePivot مع updateExistingPivot
            $query = $cart->items();

            if ($variantId && $variantId !== 'null') {
                $query->wherePivot('product_variant_id', $variantId);
            } else {
                $query->wherePivot('product_variant_id', null);
            }

            $query->updateExistingPivot($productId, [
                'quantity' => $newQuantity,
                'price' => $newPrice
            ]);
        } else {
            $this->removeFromCart($productId, $variantId);
            return;
        }

        $this->getCartItems();
        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($productId, $variantId = null)
    {
        $cart = $this->getCurrentCart();
        if (!$cart) return;

        if ($variantId && $variantId !== 'null') {
            $cart->items()->wherePivot('product_variant_id', $variantId)->detach($productId);
        } else {
            $cart->items()->wherePivot('product_variant_id', null)->detach($productId);
        }

        $this->getCartItems();
        $this->dispatch('cartUpdated');
        $cartCount=$cart->items()->count();
        $this->dispatch('cart_count_updated', count: $cartCount);
    }

    public function updateQuantity($productId, $variantId = null, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($productId, $variantId);
            return;
        }

        $cart = $this->getCurrentCart();
        if (!$cart) return;

        $existingItem = $this->findCartItemThroughRelationship($cart, $productId, $variantId);
        if ($existingItem) {
            $unitPrice = $this->getUnitPrice($productId, $variantId);
            $newPrice = $quantity * $unitPrice;

            // ✅ التصحيح: استخدام wherePivot مع updateExistingPivot
            $query = $cart->items();

            if ($variantId && $variantId !== 'null') {
                $query->wherePivot('product_variant_id', $variantId);
            } else {
                $query->wherePivot('product_variant_id', null);
            }

            $query->updateExistingPivot($productId, [
                'quantity' => $quantity,
                'price' => $newPrice
            ]);
        }

        $this->getCartItems();
        $this->dispatch('cartUpdated');
    }

    private function getCurrentCart()
    {
        $userId = auth()->guard('web')->id();
        $sessionId = session()->getId();

        $cartQuery = Cart::query();

        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }

        $cart = $cartQuery->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
            ]);
        }

        return $cart;
    }

    private function findCartItemThroughRelationship($cart, $productId, $variantId = null)
    {
        $query = $cart->items()->where('products.id', $productId);

        if ($variantId && $variantId !== 'null') {
            $query->wherePivot('product_variant_id', $variantId);
        } else {
            $query->wherePivot('product_variant_id', null);
        }

        return $query->first();
    }

    private function getUnitPrice($productId, $variantId = null)
    {
        $product = Product::find($productId);
        if ($product->has_discount) {
            if ($variantId && $variantId !== 'null') {
                $variant = ProductVariant::find($variantId);
                return $variant ? $variant->price - (($product->discount_percentage * $variant->price) / 100) : 0;
            } else {
                return $product ? $product->base_price - (($product->discount_percentage * $product->base_price) / 100) : 0;
            }
        } else {
            if ($variantId && $variantId !== 'null') {
                $variant = ProductVariant::find($variantId);
                return $variant ? $variant->price : 0;
            } else {
                return $product ? $product->base_price : 0;
            }
        }
    }

    private function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item->pivot->price;
        }

        $this->total = $this->subtotal;
    }

    public function proceedToCheckout()
    {
        if (count($this->cartItems) === 0) {
            session()->flash('error', 'Your cart is empty');
            return;
        }

        return redirect()->route('checkout');
    }

    public function getCartItemsCount()
    {
        $count = 0;
        foreach ($this->cartItems as $item) {
            $count += $item->pivot->quantity;
        }
        return $count;
    }

    public function render()
    {
        return view('livewire.cart-page', [
            'cartItemsCount' => $this->getCartItemsCount()
        ]);
    }
}
