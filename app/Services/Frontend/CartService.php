<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\CartRepository;
use App\Repositories\Frontend\ProductRepository;

class CartService
{
    public function __construct(
        private readonly CartRepository $carts,
        private readonly ProductRepository $products,
    ) {}

    public function getCartSnapshot(): array
    {
        $cart = $this->carts->findForCurrentVisitor();

        if (! $cart) {
            return [
                'cart' => null,
                'items' => [],
                'subtotal' => 0,
                'total' => 0,
                'count' => 0,
            ];
        }

        $items = $this->products->getCartItems($cart);
        $subtotal = $items->sum(fn ($item) => (float) $item->pivot->price);

        return [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'count' => $items->sum(fn ($item) => (int) $item->pivot->quantity),
        ];
    }

    public function increaseQuantity(int $productId, ?int $variantId = null): array
    {
        $cart = $this->carts->findOrCreateForCurrentVisitor();
        $existingItem = $this->carts->findItem($cart, $productId, $variantId);

        if ($existingItem) {
            $newQuantity = (int) $existingItem->pivot->quantity + 1;
            $this->carts->updateItem(
                $cart,
                $productId,
                $variantId,
                $newQuantity,
                $newQuantity * $this->getUnitPrice($productId, $variantId),
            );
        } else {
            $this->carts->attachItem(
                $cart,
                $productId,
                $variantId,
                1,
                $this->getUnitPrice($productId, $variantId),
            );
        }

        return $this->getCartSnapshot();
    }

    public function decreaseQuantity(int $productId, ?int $variantId = null): array
    {
        $cart = $this->carts->findForCurrentVisitor();

        if (! $cart) {
            return $this->getCartSnapshot();
        }

        $existingItem = $this->carts->findItem($cart, $productId, $variantId);

        if (! $existingItem) {
            return $this->getCartSnapshot();
        }

        $currentQuantity = (int) $existingItem->pivot->quantity;

        if ($currentQuantity <= 1) {
            return $this->removeItem($productId, $variantId);
        }

        $newQuantity = $currentQuantity - 1;

        $this->carts->updateItem(
            $cart,
            $productId,
            $variantId,
            $newQuantity,
            $newQuantity * $this->getUnitPrice($productId, $variantId),
        );

        return $this->getCartSnapshot();
    }

    public function updateQuantity(int $productId, ?int $variantId, int $quantity): array
    {
        if ($quantity < 1) {
            return $this->removeItem($productId, $variantId);
        }

        $cart = $this->carts->findForCurrentVisitor();

        if (! $cart) {
            return $this->getCartSnapshot();
        }

        $existingItem = $this->carts->findItem($cart, $productId, $variantId);

        if ($existingItem) {
            $this->carts->updateItem(
                $cart,
                $productId,
                $variantId,
                $quantity,
                $quantity * $this->getUnitPrice($productId, $variantId),
            );
        }

        return $this->getCartSnapshot();
    }

    public function removeItem(int $productId, ?int $variantId = null): array
    {
        $cart = $this->carts->findForCurrentVisitor();

        if (! $cart) {
            return $this->getCartSnapshot();
        }

        $this->carts->detachItem($cart, $productId, $variantId);

        return $this->getCartSnapshot();
    }

    private function getUnitPrice(int $productId, ?int $variantId = null): float
    {
        $product = $this->products->findProduct($productId);

        if (! $product) {
            return 0.0;
        }

        if ($variantId) {
            $variant = $this->products->findVariant($variantId);
            $variantPrice = (float) ($variant?->price ?? 0);

            if ($product->has_discount) {
                return $variantPrice - (($product->discount_percentage * $variantPrice) / 100);
            }

            return $variantPrice;
        }

        $basePrice = (float) $product->base_price;

        if ($product->has_discount) {
            return $basePrice - (($product->discount_percentage * $basePrice) / 100);
        }

        return $basePrice;
    }
}
