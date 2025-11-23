<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Download;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{




    public function index()
    {

        $categories = Category::has('products')->limit(8)->get();
        $brands = Brand::has('products')->limit(8)->get();

        $currencyCode = session('currency', 'EGP');
        $currency = Currency::where('code', $currencyCode)->with('products')->first();
        $blogs=Blog::latest()->limit(8)->get();

        $products = Product::query()

            // ✅ الشرط 1: يجب أن يكون المنتج نشطًا
            ->where('status', 'active')

            // ✅ الشرط 2: منطق التوفر (يشمل المتغيرات)
            ->where(function ($query) {

                // (أ) المنتجات التي لا تحتوي على متغيرات (Variants)
                $query->where('has_variants', false)
                    ->where(function ($q) {
                        // المنتجات المدارة المخزون والتي كميتها أكبر من صفر OR المنتجات غير المدارة
                        $q->where('manage_quantity', false)
                            ->orWhere(function ($q2) {
                                $q2->where('manage_quantity', true)
                                    ->where('quantity', '>', 0);
                            });
                    })




                    ->orWhere(function ($q) {
                        $q->where('has_variants', true)

                            ->whereHas('variants', function ($variantQuery) {
                                // المتغير يكون متاحًا إذا كان:
                                $variantQuery->where(function ($v) {
                                    // 1. غير مُدار المخزون OR
                                    $v->where('manage_quantity', false)
                                        // 2. مُدار المخزون والكمية > 0
                                        ->orWhere(function ($v2) {
                                            $v2->where('manage_quantity', true)
                                                ->where('quantity', '>', 0);
                                        });
                                });
                            });
                    });
            })
            ->where('currency_id', $currency->id)
            ->with(['category', 'brand', 'images', 'currency', 'variants.variantImages', 'variants.attributeValues.attribute', 'variants.attributeValuesPivot.attributeValue'])
            ->get();


        $downloads = Download::all();
        $services = Service::where('is_active', true)->with('category','baseCurrency','features')->limit(8)->get();

        return view('home', [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'downloads' => $downloads,
            'services' => $services,
            'blogs'=>$blogs,
        ]);
    }
}
