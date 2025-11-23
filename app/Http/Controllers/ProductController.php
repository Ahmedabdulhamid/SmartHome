<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with([
            'category',
            'brand',
            'images',
            'variants.variantImages',
            'variants.attributeValues.attribute',
            'currency',
            'variants.attributeValuesPivot.attributeValue'
        ])
            ->where('slug', $id)
            ->where('status', 'active') // ✅ إضافة شرط الحالة
            ->firstOrFail(); // 💡 اقتراح: استخدم firstOrFail لإرجاع 404 إذا لم يكن المنتج موجودًا أو نشطًا

        return view('products.product_details', ['product' => $product]);
    }
    public function getProductsByCategory(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $currencyCode = session('currency', 'EGP');

        $products = Product::query()
            ->where('category_id', $category->id)

            // ✅ الشرط 1: يجب أن يكون المنتج نشطًا
            ->where('status', 'active')

            // ✅ الشرط 2: منطق التوفر الشامل (المنتجات البسيطة والمركبة)
            ->where(function ($query) {

                // (أ) المنتجات البسيطة (has_variants = false): نطبق منطق المخزون عليها مباشرة
                $query->where('has_variants', false)
                    ->where(function ($q) {
                        // غير مُدار OR (مُدار والكمية > 0)
                        $q->where('manage_quantity', false)
                            ->orWhere(function ($q2) {
                                $q2->where('manage_quantity', true)
                                    ->where('quantity', '>', 0);
                            });
                    })

                    // OR

                    // (ب) المنتجات المركبة (has_variants = true): نتحقق من توفر متغير واحد على الأقل
                    ->orWhere(function ($q) {
                        $q->where('has_variants', true)
                            // يجب أن يكون لديها متغير واحد على الأقل متوفر
                            ->whereHas('variants', function ($variantQuery) {
                                // المتغير يكون متاحًا إذا كان: (غير مُدار OR (مُدار والكمية > 0))
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

            // الشرط 3: العملة
            ->whereHas('currency', function ($q) use ($currencyCode) {
                $q->where('code', $currencyCode);
            })

            // ✅ Eager Loading (ممتاز، Currency مضافة الآن)
            ->with([
                'category',
                'brand',
                'images',
                'currency',
                'variants.variantImages',
                'variants.attributeValues.attribute',
                'variants.attributeValuesPivot.attributeValue'
            ])
            ->paginate(10);

        return view('products.products', ['products' => $products]);
    }
    public function getProductsByBrands(string $slug, Request $request)
    {
        // 1. تصحيح: يجب جلب Brand وليس Category
        $brand = Brand::where('slug', $slug)->firstOrFail();
        $currencyCode = session('currency', 'EGP');

        $products = Product::query()
            // ✅ تصحيح: ربط المنتجات بالـ Brand ID
            ->where('brand_id', $brand->id)

            // ✅ الشرط 1: يجب أن يكون المنتج نشطًا
            ->where('status', 'active')

            // ✅ الشرط 2: منطق التوفر الشامل (المنتجات البسيطة والمركبة)
            ->where(function ($query) {

                // (أ) المنتجات البسيطة (has_variants = false): نطبق منطق المخزون عليها مباشرة
                $query->where('has_variants', false)
                    ->where(function ($q) {
                        // غير مُدار OR (مُدار والكمية > 0)
                        $q->where('manage_quantity', false)
                            ->orWhere(function ($q2) {
                                $q2->where('manage_quantity', true)
                                    ->where('quantity', '>', 0);
                            });
                    })

                    // OR

                    // (ب) المنتجات المركبة (has_variants = true): نتحقق من توفر متغير واحد على الأقل
                    ->orWhere(function ($q) {
                        $q->where('has_variants', true)
                            // يجب أن يكون لديها متغير واحد على الأقل متوفر
                            ->whereHas('variants', function ($variantQuery) {
                                // المتغير يكون متاحًا إذا كان: (غير مُدار OR (مُدار والكمية > 0))
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

            // الشرط 3: العملة
            ->whereHas('currency', function ($q) use ($currencyCode) {
                $q->where('code', $currencyCode);
            })

            // Eager Loading جيد جداً هنا
            ->with([
                'category',
                'brand',
                'images',
                'currency',
                'variants.variantImages',
                'variants.attributeValues.attribute',
                'variants.attributeValuesPivot.attributeValue'
            ])
            ->paginate(10);

        return view('products.products', ['products' => $products]);
    }


    public function getAllProducts()
    {
        $currencyCode = session('currency', 'EGP');

        $products = Product::query()
            // ✅ الشرط 1: يجب أن يكون المنتج نشطًا
            ->where('status', 'active')

            // ✅ الشرط 2: منطق التوفر الشامل (المنتجات البسيطة والمركبة)
            ->where(function ($query) {

                // (أ) المنتجات البسيطة (has_variants = false)
                $query->where('has_variants', false)
                    ->where(function ($q) {
                        // غير مُدار OR (مُدار والكمية > 0)
                        $q->where('manage_quantity', false)
                            ->orWhere(function ($q2) {
                                $q2->where('manage_quantity', true)
                                    ->where('quantity', '>', 0);
                            });
                    })

                    // OR

                    // (ب) المنتجات المركبة (has_variants = true)
                    ->orWhere(function ($q) {
                        $q->where('has_variants', true)
                            // يجب أن يكون لديها متغير واحد على الأقل متوفر
                            ->whereHas('variants', function ($variantQuery) {
                                // المتغير يكون متاحًا إذا كان: (غير مُدار OR (مُدار والكمية > 0))
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

            // ✅ التعديل 1: إضافة شرط العملة
            ->whereHas('currency', function ($q) use ($currencyCode) {
                $q->where('code', $currencyCode);
            })

            // ✅ التعديل 2: إضافة Eager Loading الضروري
            ->with(['images', 'variants', 'category', 'brand', 'currency'])

            // ✅ التعديل 3: استخدام Pagination بدلاً من get()
            ->paginate(10);

        return view('pages.products', ['products' => $products]);
    }
    public function getAllCategories()
    {
        $categories = Category::whereNull('parent_id')
            ->has('products')
            ->paginate(10);

        return view('pages.categories', ['categories' => $categories]);
    }
    public function getAllBrands()
    {
        $brands = Brand::has('products')->paginate(10);

        return view('pages.brands', ['brands' => $brands]);
    }
}
