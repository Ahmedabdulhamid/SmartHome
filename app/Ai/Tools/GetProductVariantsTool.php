<?php

namespace App\Ai\Tools;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Arr;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProductVariantsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Get all variants for a product using product_id or product_slug.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $locale = $this->normalizeLocale((string) $request->string('locale', app()->getLocale()));
        $productId = $request->integer('product_id');
        $productSlug = trim((string) $request->string('product_slug'));

        $productQuery = Product::query()->with(['currency:id,code,symbol']);

        if ($productId > 0) {
            $productQuery->whereKey($productId);
        } elseif ($productSlug !== '') {
            $productQuery->where('slug', $productSlug);
        } else {
            return 'Please provide product_id or product_slug.';
        }

        $product = $productQuery->first();

        if (! $product) {
            return 'Product not found.';
        }

        $variants = ProductVariant::query()
            ->where('product_id', $product->id)
            ->with(['attributeValues.attribute:id,name'])
            ->orderBy('id')
            ->get();

        if ($variants->isEmpty()) {
            return 'This product has no variants.';
        }

        $currency = $product->currency->code ?? $product->currency->symbol ?? null;

        $result = [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $this->localizedValue($product->name, $locale),
                'currency' => $currency,
            ],
            'variants' => $variants->map(function (ProductVariant $variant) use ($locale) {
                return [
                    'id' => $variant->id,
                    'name' => $this->localizedValue($variant->name, $locale),
                    'price' => $variant->price,
                    'actual_price' => $variant->actual_price,
                    'available_quantity' => $variant->manage_quantity
                        ? max(0, (int) $variant->quantity - (int) $variant->reserved_stock)
                        : null,
                    'highlights' => $this->localizedList($variant->highlights, $locale),
                    'drawbacks' => $this->localizedList($variant->drawbacks, $locale),
                    'attributes' => $variant->attributeValues
                        ->map(function ($attributeValue) use ($locale) {
                            return [
                                'attribute' => $this->localizedValue($attributeValue->attribute?->name, $locale),
                                'value' => $attributeValue->value,
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })->values()->all(),
        ];

        return "Product variants:\n".json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'product_id' => $schema->integer()->description('Product numeric ID.'),
            'product_slug' => $schema->string()->description('Product slug, used when product_id is not provided.'),
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

    private function localizedList(mixed $value, string $locale): array
    {
        $localized = $this->localizedValue($value, $locale);

        if (is_array($localized)) {
            return array_values(array_filter($localized, fn ($item) => $item !== null && $item !== ''));
        }

        if (is_string($localized) && trim($localized) !== '') {
            return [trim($localized)];
        }

        return [];
    }
}
