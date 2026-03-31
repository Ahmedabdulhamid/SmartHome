<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\FrontendCache;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = FrontendCache::remember('product_detail', [
            'slug' => $id,
            'locale' => app()->getLocale(),
        ], 900, function () use ($id) {
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
                ->where('slug', $id)
                ->where('status', 'active')
                ->firstOrFail();
        });

        return view('products.product_details', ['product' => $product]);
    }

    public function getProductsByCategory(string $slug, Request $request)
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $currencyCode = session('currency', 'EGP');

        $products = FrontendCache::remember('products_by_category', [
            'slug' => $slug,
            'currency' => $currencyCode,
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 900, function () use ($category, $currencyCode) {
            return $this->productListingQuery()
                ->where('category_id', $category->id)
                ->whereHas('currency', function ($q) use ($currencyCode) {
                    $q->where('code', $currencyCode);
                })
                ->paginate(10);
        });

        return view('products.products', ['products' => $products]);
    }

    public function getProductsByBrands(string $slug, Request $request)
    {
        $brand = Brand::query()->where('slug', $slug)->firstOrFail();
        $currencyCode = session('currency', 'EGP');

        $products = FrontendCache::remember('products_by_brand', [
            'slug' => $slug,
            'currency' => $currencyCode,
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 900, function () use ($brand, $currencyCode) {
            return $this->productListingQuery()
                ->where('brand_id', $brand->id)
                ->whereHas('currency', function ($q) use ($currencyCode) {
                    $q->where('code', $currencyCode);
                })
                ->paginate(10);
        });

        return view('products.products', ['products' => $products]);
    }

    public function getAllProducts(Request $request)
    {
        $currencyCode = session('currency', 'EGP');

        $products = FrontendCache::remember('all_products', [
            'currency' => $currencyCode,
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 900, function () use ($currencyCode) {
            return $this->productListingQuery()
                ->whereHas('currency', function ($q) use ($currencyCode) {
                    $q->where('code', $currencyCode);
                })
                ->with(['images', 'firstImage', 'variants', 'category', 'brand', 'currency'])
                ->paginate(10);
        });

        return view('pages.products', ['products' => $products]);
    }

    public function getAllCategories(Request $request)
    {
        $categories = FrontendCache::remember('all_categories', [
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 1800, function () {
            return Category::query()
                ->whereNull('parent_id')
                ->has('products')
                ->paginate(10);
        });

        return view('pages.categories', ['categories' => $categories]);
    }

    public function getAllBrands(Request $request)
    {
        $brands = FrontendCache::remember('all_brands', [
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 1800, function () {
            return Brand::query()
                ->has('products')
                ->paginate(10);
        });

        return view('pages.brands', ['brands' => $brands]);
    }

    private function productListingQuery()
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
