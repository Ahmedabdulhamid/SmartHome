<?php

namespace App\Ai\Tools;

use App\Models\Product;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Arr;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchProductsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search products by name, slug, or description and return key sales details.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = trim((string) $request->string('query'));
        $locale = $this->normalizeLocale((string) $request->string('locale', app()->getLocale()));
        $limit = max(1, min(20, $request->integer('limit', 5)));
        $includeVariants = $request->boolean('include_variants', false);

        $products = Product::query()
            ->with(['currency:id,code,symbol', 'variants:id,product_id,name,price,quantity,manage_quantity,reserved_stock'])
            ->where('status', 'active')
            ->when($query !== '', function ($builder) use ($query) {
                $like = '%'.$query.'%';

                $builder->where(function ($inner) use ($like) {
                    $inner->where('slug', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('name->en', 'like', $like)
                        ->orWhere('name->ar', 'like', $like);
                });
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($products->isEmpty()) {
            return 'No products found for this query.';
        }

        $result = $products->map(function (Product $product) use ($locale, $includeVariants) {
            $currency = $product->currency->code ?? $product->currency->symbol ?? null;
            $availableQuantity = $product->manage_quantity
                ? max(0, (int) $product->quantity - (int) $product->reserved_stock)
                : null;

            $data = [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $this->localizedValue($product->name, $locale),
                'description' => $this->localizedValue($product->description, $locale),
                'status' => $product->status,
                'has_variants' => (bool) $product->has_variants,
                'currency' => $currency,
                'base_price' => $product->base_price,
                'actual_price' => $product->actual_price,
                'available_quantity' => $availableQuantity,
                'highlights' => $this->localizedList($product->highlights, $locale),
                'drawbacks' => $this->localizedList($product->drawbacks, $locale),
            ];

            if ($includeVariants) {
                $data['variants'] = $product->variants
                    ->map(function ($variant) use ($locale) {
                        return [
                            'id' => $variant->id,
                            'name' => $this->localizedValue($variant->name, $locale),
                            'price' => $variant->price,
                            'available_quantity' => $variant->manage_quantity
                                ? max(0, (int) $variant->quantity - (int) $variant->reserved_stock)
                                : null,
                        ];
                    })
                    ->values()
                    ->all();
            }

            return $data;
        })->values();

        return "Products found:\n".$result->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Text to search in product name, slug, or description.'),
            'locale' => $schema->string()->description('Response language for translatable fields, e.g. ar or en.'),
            'limit' => $schema->integer()->min(1)->max(20)->description('Maximum number of products to return.'),
            'include_variants' => $schema->boolean()->description('Include light variant summaries in each product result.'),
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
