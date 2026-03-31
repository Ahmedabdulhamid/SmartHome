<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Download;
use App\Models\Product;
use App\Models\Service;
use App\Support\FrontendCache;

class HomeController extends Controller
{
    public function index()
    {
        $currencyCode = session('currency', 'EGP');

        $homeData = FrontendCache::remember('home_page', [
            'locale' => app()->getLocale(),
            'currency' => $currencyCode,
        ], 900, function () use ($currencyCode) {
            $currency = Currency::query()
                ->where('code', $currencyCode)
                ->firstOrFail();

            $products = Product::query()
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
                ->where('currency_id', $currency->id)
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

            return [
                'categories' => Category::query()->has('products')->limit(8)->get(),
                'brands' => Brand::query()->has('products')->limit(8)->get(),
                'products' => $products,
                'downloads' => Download::query()->get(),
                'services' => Service::query()
                    ->where('is_active', true)
                    ->with('category', 'baseCurrency', 'features')
                    ->limit(8)
                    ->get(),
                'blogs' => Blog::query()
                    ->with(['category', 'author'])
                    ->latest()
                    ->limit(8)
                    ->get(),
            ];
        });

        return view('home', $homeData);
    }
}
