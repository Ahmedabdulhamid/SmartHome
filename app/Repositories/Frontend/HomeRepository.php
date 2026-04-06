<?php

namespace App\Repositories\Frontend;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Download;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class HomeRepository
{
    public function findCurrencyByCode(string $code): Currency
    {
        return Currency::query()
            ->where('code', $code)
            ->firstOrFail();
    }

    public function getHomeProductsByCurrency(int $currencyId): Collection
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
            ->where('currency_id', $currencyId)
            ->with([
                'category',
                'brand',
                'images',
                'firstImage',
                'currency',
                'variants.variantImages',
                'variants.attributeValues.attribute',
                'variants.attributeValuesPivot.attributeValue',
            ])
            ->get();
    }

    public function getFeaturedCategories(int $limit = 8): Collection
    {
        return Category::query()->has('products')->limit($limit)->get();
    }

    public function getFeaturedBrands(int $limit = 8): Collection
    {
        return Brand::query()->has('products')->limit($limit)->get();
    }

    public function getDownloads(): Collection
    {
        return Download::query()->get();
    }

    public function getFeaturedServices(int $limit = 8): Collection
    {
        return Service::query()
            ->where('is_active', true)
            ->with('category', 'baseCurrency', 'features')
            ->limit($limit)
            ->get();
    }

    public function getLatestBlogs(int $limit = 8): Collection
    {
        return Blog::query()
            ->with(['category', 'author'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
