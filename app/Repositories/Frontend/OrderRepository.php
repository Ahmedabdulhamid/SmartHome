<?php

namespace App\Repositories\Frontend;

use App\Models\Order;

class OrderRepository
{
    public function create(array $attributes): Order
    {
        return Order::query()->create($attributes);
    }

    public function createItem(Order $order, array $attributes): void
    {
        $order->items()->create($attributes);
    }
}
