<?php

namespace App\Ai\Tools;

use App\Models\Order;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Arr;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class LookupOrderTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Look up an order by order number with customer email or phone and return status/details.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $orderId = $request->integer('order_id');
        $contact = trim((string) $request->string('contact'));
        $locale = $this->normalizeLocale((string) $request->string('locale', app()->getLocale()));

        if ($orderId <= 0) {
            return 'order_id is required.';
        }

        if ($contact === '') {
            return 'contact is required (customer email or phone).';
        }

        $order = Order::query()
            ->with(['currency:id,code,symbol', 'items.product:id,name,slug', 'items.variant:id,name'])
            ->whereKey($orderId)
            ->where(function ($query) use ($contact) {
                $query->where('email', $contact)
                    ->orWhere('phone', $contact);
            })
            ->first();

        if (! $order) {
            return 'Order not found with this order_id and contact.';
        }

        $currency = $order->currency->code ?? $order->currency->symbol ?? null;

        $result = [
            'order_id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'shipping_price' => $order->shipping_price,
            'currency' => $currency,
            'created_at' => optional($order->created_at)?->toDateTimeString(),
            'updated_at' => optional($order->updated_at)?->toDateTimeString(),
            'items' => $order->items->map(function ($item) use ($locale) {
                return [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'product_name' => $this->localizedValue($item->product_name ?: $item->product?->name, $locale),
                    'variant_name' => $this->localizedValue($item->variant_name ?: $item->variant?->name, $locale),
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'line_total' => $item->total,
                ];
            })->values()->all(),
        ];

        return "Order details:\n".json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'order_id' => $schema->integer()->required()->description('Order number / ID.'),
            'contact' => $schema->string()->required()->description('Customer email or phone used in checkout.'),
            'locale' => $schema->string()->description('Response language for translatable fields, e.g. ar or en.'),
        ];
    }

    private function normalizeLocale(string $locale): string
    {
        $locale = strtolower(trim($locale));

        return in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    }

    private function localizedValue(mixed $value, string $locale): mixed
    {
        if (is_array($value)) {
            return Arr::get($value, $locale)
                ?? Arr::get($value, 'en')
                ?? Arr::first($value);
        }

        return $value;
    }
}
