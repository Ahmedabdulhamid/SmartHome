<?php

namespace App\Http\Controllers;

use App\Services\Frontend\ProductCatalogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductCatalogService $products,
    ) {}

    public function show($id)
    {
        $product = $this->products->getProductDetails($id, app()->getLocale());

        return view('products.product_details', ['product' => $product]);
    }

    public function getProductsByCategory(string $slug, Request $request)
    {
        $currencyCode = session('currency', 'EGP');
        $products = $this->products->getProductsByCategory(
            $slug,
            $currencyCode,
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('products.products', ['products' => $products]);
    }

    public function getProductsByBrands(string $slug, Request $request)
    {
        $currencyCode = session('currency', 'EGP');
        $products = $this->products->getProductsByBrand(
            $slug,
            $currencyCode,
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('products.products', ['products' => $products]);
    }

    public function getAllProducts(Request $request)
    {
        $currencyCode = session('currency', 'EGP');
        $products = $this->products->getAllProducts(
            $currencyCode,
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('pages.products', ['products' => $products]);
    }

    public function getAllCategories(Request $request)
    {
        $categories = $this->products->getAllCategories(
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('pages.categories', ['categories' => $categories]);
    }

    public function getAllBrands(Request $request)
    {
        $brands = $this->products->getAllBrands(
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('pages.brands', ['brands' => $brands]);
    }
}
