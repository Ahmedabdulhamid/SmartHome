<?php

namespace App\Services\Frontend;

use App\Events\OrderCreated;
use App\Models\Admin;
use App\Repositories\Frontend\CartRepository;
use App\Repositories\Frontend\OrderRepository;
use App\Repositories\Frontend\ShippingRepository;
use Filament\Notifications\Notification;

class CheckoutService
{
    public function __construct(
        private readonly CartRepository $carts,
        private readonly OrderRepository $orders,
        private readonly ShippingRepository $shipping,
    ) {}

    public function getCheckoutData(?int $governorateId, ?int $cityId, ?string $shippingType): array
    {
        $shippingPrice = $this->shipping->findShippingPrice($governorateId, $cityId, $shippingType);

        return [
            'governorates' => $this->shipping->getGovernorates(),
            'cities' => $this->shipping->getCitiesByGovernorate($governorateId),
            'paymMethods' => $this->shipping->getPaymentMethods(),
            'shippingPrice' => $shippingPrice,
        ];
    }

    public function submit(array $validated, float $shippingPrice): array
    {
        $cart = $this->carts->findForCurrentVisitor();

        abort_if(! $cart || $cart->items()->count() === 0, 422, 'Cart is empty.');

        $cartItems = $cart->items()->with('currency')->get();
        $subtotal = $cartItems->sum(fn ($item) => (float) $item->pivot->price);
        $currencyId = $cartItems->first()?->currency?->id;

        $order = $this->orders->create([
            'user_id' => auth()->guard('web')->user()?->id,
            'payment_method_id' => $validated['paym_method'],
            'f_name' => $validated['f_name'],
            'l_name' => $validated['l_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'governorate_id' => $validated['govoernorateId'],
            'city_id' => $validated['cityId'],
            'status' => 'pending',
            'shipping_price' => $shippingPrice,
            'currency_id' => $currencyId,
            'total_amount' => $subtotal + $shippingPrice,
            'zip_code' => $validated['zip_code'],
        ]);

        foreach ($cartItems as $item) {
            $this->orders->createItem($order, [
                'order_id' => $order->id,
                'product_id' => $item->pivot->product_id,
                'product_variant_id' => $item->pivot->product_variant_id,
                'price' => $item->pivot->price,
                'quantity' => $item->pivot->quantity,
                'total' => $item->pivot->quantity * $item->pivot->price,
                'currency_id' => $item->currency->id,
            ]);
        }

        $this->carts->delete($cart);

        $admins = Admin::query()->get();

        foreach ($admins as $admin) {
            event(new OrderCreated($order, $admin));

            Notification::make()
                ->title('New Order Created')
                ->broadcast($admin)
                ->sendToDatabase($admin);
        }

        return [
            'order' => $order,
            'cart_count' => 0,
        ];
    }
}
