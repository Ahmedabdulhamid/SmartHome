<?php

namespace App\Repositories\Frontend;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function findActiveBySlug(string $slug): Product
    {
        return Product::query()
            ->with([
                'category',
                'brand',
                'images',
                'firstImage',
                'variants.variantImages',
                'variants.attributeValues.attribute',
                'currency',
                'variants.attributeValuesPivot.attributeValue',
            ])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
    }

    public function findCategoryBySlug(string $slug): Category
    {
        return Category::query()->where('slug', $slug)->firstOrFail();
    }

    public function findBrandBySlug(string $slug): Brand
    {
        return Brand::query()->where('slug', $slug)->firstOrFail();
    }

    public function paginateByCategoryAndCurrency(int $categoryId, string $currencyCode, int $perPage = 10): LengthAwarePaginator
    {
        return $this->listingQuery()
            ->where('category_id', $categoryId)
            ->whereHas('currency', fn (Builder $query) => $query->where('code', $currencyCode))
            ->paginate($perPage);
    }

    public function paginateByBrandAndCurrency(int $brandId, string $currencyCode, int $perPage = 10): LengthAwarePaginator
    {
        return $this->listingQuery()
            ->where('brand_id', $brandId)
            ->whereHas('currency', fn (Builder $query) => $query->where('code', $currencyCode))
            ->paginate($perPage);
    }

    public function paginateAllByCurrency(string $currencyCode, int $perPage = 10): LengthAwarePaginator
    {
        return $this->listingQuery()
            ->whereHas('currency', fn (Builder $query) => $query->where('code', $currencyCode))
            ->with(['images', 'firstImage', 'variants', 'category', 'brand', 'currency'])
            ->paginate($perPage);
    }

    public function paginateRootCategories(int $perPage = 10): LengthAwarePaginator
    {
        return Category::query()
            ->whereNull('parent_id')
            ->has('products')
            ->paginate($perPage);
    }

    public function paginateBrands(int $perPage = 10): LengthAwarePaginator
    {
        return Brand::query()
            ->has('products')
            ->paginate($perPage);
    }

    public function listByCurrency(string $currencyCode): Collection
    {
        return Product::query()
            ->whereHas('currency', fn (Builder $query) => $query->where('code', $currencyCode))
            ->get();
    }

    public function findProduct(int $productId): ?Product
    {
        return Product::query()->with('variants')->find($productId);
    }

    public function findVariant(int $variantId): ?ProductVariant
    {
        return ProductVariant::query()
            ->with(['product', 'attributeValuesPivot.attribute', 'attributeValuesPivot.attributeValue'])
            ->find($variantId);
    }

    public function getVariantsForProduct(int $productId): Collection
    {
        return ProductVariant::query()
            ->where('product_id', $productId)
            ->with(['attributeValuesPivot.attribute', 'attributeValuesPivot.attributeValue'])
            ->get();
    }

    public function getCartItems(Cart $cart): Collection
    {
        return $cart->items()->with(['currency', 'images'])->get();
    }

    private function listingQuery(): Builder
    {
        return Product::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('has_variants', false)
                    ->where(function ($q) {
                        $q->where('manage_quantity', false)
                            ->orWhere(function ($q2) {
                                $q2->where('manage_quantity', true)
                                    ->where('quantity', '>', 0);
                            });
                    })
                    ->orWhere(function ($q) {
                        $q->where('has_variants', true)
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
            ->with([
                'category',
                'brand',
                'images',
                'firstImage',
                'currency',
                'variants.variantImages',
                'variants.attributeValues.attribute',
                'variants.attributeValuesPivot.attributeValue',
            ]);
    }
}
