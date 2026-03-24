<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\SalesAgentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\UserProfileController;
use App\Mail\ContactReplyMail;
use App\Models\Blog;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\Quotation;
use App\Models\Service;
use Flasher\Laravel\Facade\Flasher;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

Route::get('lang/{locale}', function ($locale) {
    // التحقق من أن اللغة مدعومة لتجنب المشاكل الأمنية
    if (! in_array($locale, ['en', 'ar'])) {
        abort(400);
    }

    session()->put('locale', $locale);

    // لإجبار Laravel على استخدام اللغة الجديدة فورًا
    app()->setLocale($locale);

    // العودة للصفحة السابقة
    return redirect()->back();
})->name('locale.switch');
// صفحات عامة للزوار
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/downloads', [HomeController::class, 'index'])->name('downloads.index');
Route::get('/set-currency/{code}', function ($code) {
    session(['currency' => $code]);
    return redirect()->back();
});
Route::get('about-us', function () {
    return view('pages.about-us');
})->name('about.us');
Route::get('contact-us', function () {
    return view('pages.contact-us');
})->name('contact.us');
Route::get('/test-flash', function () {
    flash()->addSuccess('✅ تم العرض بنجاح بواسطة Flasher');
    flash()->addError('❌ هذا مثال على رسالة خطأ');
    return redirect()->back();
});
Route::get('categories/{slug}/products', [ProductController::class, 'getProductsByCategory'])->name('products.categories');
Route::get('brands/{slug}/products', [ProductController::class, 'getProductsByBrands'])->name('products.brands');

Route::get('rfq', [RfqController::class, 'index'])->name('rfq');
Route::get('sales-agent', [SalesAgentController::class, 'index'])->name('sales.agent.index');
Route::post('sales-agent/ask', [SalesAgentController::class, 'ask'])->name('sales.agent.ask')->middleware('throttle:ai');
Route::delete('sales-agent/conversation', [SalesAgentController::class, 'resetConversation'])->name('sales.agent.reset');
Route::get('/debug-php', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'php_binary'  => PHP_BINARY,
        'loaded_ini'  => php_ini_loaded_file(),
        'curl_cainfo' => ini_get('curl.cainfo'),
        'openssl_cafile' => ini_get('openssl.cafile'),
    ]);
});
Route::get('/downloads/{download}', [HomeController::class, 'show'])->name('downloads.show');
Route::get('/categories', [ProductController::class, 'getAllCategories'])
    ->name('categories');

Route::get('/services', [ServiceController::class, 'getAllServices'])
    ->name('services');

Route::get('services/{slug}', [ServiceController::class, 'getServiceBySlug'])->name('services.show');


Route::get('/blogs', [BlogController::class, 'getAllBlogs'])
    ->name('blogs');

Route::get('blogs/{slug}', [BlogController::class, 'getBlogBySlug'])->name('blogs.show');

Route::get('/faqs', function () {
    $faqs = App\Models\Faq::all();
    return view('pages.faqs', compact('faqs'));
})->name('faqs');


Route::get('/brands', [ProductController::class, 'getAllBrands'])
    ->name('brands');
Route::get('/products', [ProductController::class, 'getAllProducts'])
    ->name('products');
Route::get('/pages/{slug}', [PageController::class, 'goToPage'])
    ->name('pages');
Route::get('product-details/{id}', [ProductController::class, 'show'])->name('product.details');
Route::get('/dashboard', function () {
    $orders = auth()->guard('web')->user()
        ->orders()
        ->with(['currency', 'items'])
        ->latest()
        ->paginate(10);

    return view('dashboard', compact('orders'));
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/orders/{order}', function (Order $order) {
    abort_unless($order->user_id === auth()->guard('web')->id(), 403);

    $order->load([
        'currency',
        'items.currency',
        'items.product',
        'items.variant',
    ]);

    return view('orders.show', compact('order'));
})->middleware(['auth', 'verified'])->name('orders.show');
Route::get('cart', function () {
    return view('pages.cart');
})->name('cart');
Route::get('/quotation/download-signed/{quotation}', function (Quotation $quotation) {
    // ... (منطق التحقق والتوليد) ...
    $filePath = $quotation->storePrivatePdf();

    // 💡 إضافة DocBlock هنا لإرضاء المحرر
    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('local');

    if (empty($filePath) || !$disk->exists($filePath)) {
        abort(404, 'File not found or failed to generate.');
    }

    // الآن لن يعرض المحرر خطأً لأننا عرفنا نوع $disk
    return $disk->download(
        $filePath,
        'Quotation-' . $quotation->id . '.pdf'
    );
})->name('quotation.signed.download')->middleware(['web', 'signed']);
Route::get('checkout', function () {
    return view('pages.checkout');
})->name('checkout');
Route::get('email-preview', function () {
    // قم بتمرير نص رسالة تجريبي لـ $replyMessage
    $sampleReply = 'مرحباً، هذه رسالة تجريبية لمعاينة تصميم الإيميل الجديد وضمان أن الألوان والمحاذاة العربية تعمل بشكل مثالي.';

    // قم بإنشاء وإرجاع نسخة من كلاس الـ Mailable
    return new ContactReplyMail($sampleReply);
});
Route::middleware('guest')->group(function () {
    Route::get('/auth/{social}/login', [SocialiteController::class, 'login'])->name('auth.social.login');
    Route::get('/auth/{social}/callback', [SocialiteController::class, 'callback'])->name('auth.social.callback');
});
Route::middleware('auth')->group(function () {
    Route::get('chat-app', function () {
        return view('pages.chat-app');
    })->name('chatapp');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password/change', [UserProfileController::class, 'changePassword'])->name('password.change');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
});


Route::get('sitemap.xml', function () {

    $sitemap = SitemapGenerator::create(config('app.url'))
        ->getSitemap();


    $sitemap->add(Url::create('/')->setPriority(1.0)->setLastModificationDate(Carbon::now()));
    $sitemap->add(Url::create('/services')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
    $sitemap->add(Url::create('/blogs')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
    $sitemap->add(Url::create('/products')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
    $sitemap->add(Url::create('/contact-us')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));


    $services = Service::all();
    foreach ($services as $service) {
        $sitemap->add(Url::create(route('services.show', $service->slug))
            ->setPriority(0.9)
            ->setLastModificationDate($service->updated_at));
    }


    $blogs = Blog::all();
    foreach ($blogs as $blog) {
        $sitemap->add(Url::create(route('blogs.show', $blog->slug))
            ->setPriority(0.8) // أهمية أقل قليلاً من الخدمات
            ->setLastModificationDate($blog->updated_at));
    }


    $products = Product::all();
    foreach ($products as $product) {
        $sitemap->add(Url::create(route('product.details', $product->id))
            ->setPriority(0.7)
            ->setLastModificationDate($product->updated_at));
    }

    // 6. عرض ملف الـ Sitemap بصيغة XML
    return $sitemap->render();
})->name('sitemap.xml');



require __DIR__ . '/auth.php';
