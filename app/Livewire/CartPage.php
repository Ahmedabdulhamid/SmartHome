<?php

namespace App\Livewire;

use App\Http\Requests\Livewire\CartItemMutationRequest;
use App\Services\Frontend\CartService;
use App\Support\Livewire\ValidatesWithFormRequest;
use Livewire\Component;

class CartPage extends Component
{
    use ValidatesWithFormRequest;

    public $cartItems = [];
    public $subtotal = 0;
    public $total = 0;

    protected $listeners = ['cartUpdated' => 'getCartItems'];

    public function mount(): void
    {
        $this->hydrateCart(app(CartService::class)->getCartSnapshot());
    }

    public function getCartItems(): void
    {
        $this->hydrateCart(app(CartService::class)->getCartSnapshot());
    }

    public function increaseQty($productId, $variantId = null): void
    {
        $validated = $this->validateCartMutation($productId, $variantId);
        $this->hydrateCart(app(CartService::class)->increaseQuantity($validated['product_id'], $validated['product_variant_id'] ?? null));
        $this->dispatch('cartUpdated');
    }

    public function decreaseQty($productId, $variantId = null): void
    {
        $validated = $this->validateCartMutation($productId, $variantId);
        $this->hydrateCart(app(CartService::class)->decreaseQuantity($validated['product_id'], $validated['product_variant_id'] ?? null));
        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($productId, $variantId = null): void
    {
        $validated = $this->validateCartMutation($productId, $variantId);
        $snapshot = app(CartService::class)->removeItem($validated['product_id'], $validated['product_variant_id'] ?? null);
        $this->hydrateCart($snapshot);
        $this->dispatch('cartUpdated');
        $this->dispatch('cart_count_updated', count: $snapshot['count']);
    }

    public function updateQuantity($productId, $variantId = null, $quantity = 1): void
    {
        $validated = $this->validateCartMutation($productId, $variantId, $quantity);
        $this->hydrateCart(
            app(CartService::class)->updateQuantity(
                $validated['product_id'],
                $validated['product_variant_id'] ?? null,
                $validated['quantity'],
            )
        );
        $this->dispatch('cartUpdated');
    }

    public function proceedToCheckout()
    {
        if (count($this->cartItems) === 0) {
            session()->flash('error', 'Your cart is empty');
            return;
        }

        return redirect()->route('checkout');
    }

    public function getCartItemsCount(): int
    {
        return collect($this->cartItems)->sum(fn ($item) => (int) $item->pivot->quantity);
    }

    public function render()
    {
        return view('livewire.cart-page', [
            'cartItemsCount' => $this->getCartItemsCount(),
        ]);
    }

    private function validateCartMutation($productId, $variantId = null, $quantity = null): array
    {
        $data = [
            'product_id' => (int) $productId,
            'product_variant_id' => $this->normalizeVariantId($variantId),
        ];

        if ($quantity !== null) {
            $data['quantity'] = (int) $quantity;
        }

        return $this->validateWithFormRequest(CartItemMutationRequest::class, $data);
    }

    private function normalizeVariantId($variantId): ?int
    {
        if ($variantId === null || $variantId === '' || $variantId === 'null') {
            return null;
        }

        return (int) $variantId;
    }

    private function hydrateCart(array $snapshot): void
    {
        $this->cartItems = $snapshot['items'];
        $this->subtotal = $snapshot['subtotal'];
        $this->total = $snapshot['total'];
    }
}
