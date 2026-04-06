<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\ProductRepository;
use App\Support\FrontendCache;

class ProductCatalogService
{
    public function __construct(
        private readonly ProductRepository $products,
    ) {}

    public function getProductDetails(string $slug, string $locale)
    {
        return FrontendCache::remember('product_detail', [
            'slug' => $slug,
            'locale' => $locale,
        ], 900, fn () => $this->products->findActiveBySlug($slug));
    }

    public function getProductsByCategory(string $slug, string $currencyCode, string $locale, int $page)
    {
        return FrontendCache::remember('products_by_category', [
            'slug' => $slug,
            'currency' => $currencyCode,
            'locale' => $locale,
            'page' => $page,
        ], 900, function () use ($slug, $currencyCode) {
            $category = $this->products->findCategoryBySlug($slug);

            return $this->products->paginateByCategoryAndCurrency($category->id, $currencyCode);
        });
    }

    public function getProductsByBrand(string $slug, string $currencyCode, string $locale, int $page)
    {
        return FrontendCache::remember('products_by_brand', [
            'slug' => $slug,
            'currency' => $currencyCode,
            'locale' => $locale,
            'page' => $page,
        ], 900, function () use ($slug, $currencyCode) {
            $brand = $this->products->findBrandBySlug($slug);

            return $this->products->paginateByBrandAndCurrency($brand->id, $currencyCode);
        });
    }

    public function getAllProducts(string $currencyCode, string $locale, int $page)
    {
        return FrontendCache::remember('all_products', [
            'currency' => $currencyCode,
            'locale' => $locale,
            'page' => $page,
        ], 900, fn () => $this->products->paginateAllByCurrency($currencyCode));
    }

    public function getAllCategories(string $locale, int $page)
    {
        return FrontendCache::remember('all_categories', [
            'locale' => $locale,
            'page' => $page,
        ], 1800, fn () => $this->products->paginateRootCategories());
    }

    public function getAllBrands(string $locale, int $page)
    {
        return FrontendCache::remember('all_brands', [
            'locale' => $locale,
            'page' => $page,
        ], 1800, fn () => $this->products->paginateBrands());
    }

    public function getRfqProducts(string $currencyCode)
    {
        return $this->products->listByCurrency($currencyCode);
    }

    public function getProduct(int $productId)
    {
        return $this->products->findProduct($productId);
    }

    public function getProductVariant(int $variantId)
    {
        return $this->products->findVariant($variantId);
    }

    public function getVariantsForProduct(int $productId)
    {
        return $this->products->getVariantsForProduct($productId);
    }
}
