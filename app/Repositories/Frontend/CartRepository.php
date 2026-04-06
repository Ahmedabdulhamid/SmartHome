<?php

namespace App\Repositories\Frontend;

use App\Models\Cart;
use App\Models\Product;

class CartRepository
{
    public function findOrCreateForCurrentVisitor(): Cart
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

        if ($cart) {
            return $cart;
        }

        return Cart::query()->create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
        ]);
    }

    public function findForCurrentVisitor(): ?Cart
    {
        $userId = auth()->guard('web')->id();
        $sessionId = session()->getId();

        return Cart::query()
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->when(! $userId, fn ($query) => $query->where('session_id', $sessionId))
            ->first();
    }

    public function findItem(Cart $cart, int $productId, ?int $variantId = null): ?Product
    {
        return $cart->items()
            ->where('products.id', $productId)
            ->when($variantId, fn ($query) => $query->wherePivot('product_variant_id', $variantId))
            ->when(! $variantId, fn ($query) => $query->wherePivot('product_variant_id', null))
            ->first();
    }

    public function updateItem(Cart $cart, int $productId, ?int $variantId, int $quantity, float $price): void
    {
        $query = $cart->items();

        if ($variantId) {
            $query->wherePivot('product_variant_id', $variantId);
        } else {
            $query->wherePivot('product_variant_id', null);
        }

        $query->updateExistingPivot($productId, [
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }

    public function attachItem(Cart $cart, int $productId, ?int $variantId, int $quantity, float $price): void
    {
        $cart->items()->attach($productId, [
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }

    public function detachItem(Cart $cart, int $productId, ?int $variantId): void
    {
        $query = $cart->items();

        if ($variantId) {
            $query->wherePivot('product_variant_id', $variantId);
        } else {
            $query->wherePivot('product_variant_id', null);
        }

        $query->detach($productId);
    }

    public function delete(Cart $cart): void
    {
        $cart->delete();
    }
}
