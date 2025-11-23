<?php

namespace App\Models;

use Illuminate\Container\Attributes\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Spatie\Translatable\HasTranslations;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
class Quotation extends Model
{
    use HasTranslations;
    protected $fillable = [
        'title',
        'template_id',
        'date',
        'total',
        'delivery_place',
        'delivery_time',
        'is_reserved',
        "currency_id",
        'manual_base_price',
        'manual_margin_percentage',
        "rfq_id",
        "tax_id"
    ];
    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    protected $casts = ['title' => "array"];
    protected $translatable = ['title'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
   public function storePrivatePdf(): string
    {
        try {
            // 1. توليد محتوى الـ PDF الخام
            $pdfContent = $this->generatePdfContent();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PDF generation failed for Quotation {$this->id}: " . $e->getMessage());
            return '';
        }

        // 2. تحديد اسم الملف ومساره
        // نستخدم 'time()' لضمان التفرد وتجنب مشاكل الكاش
        $fileName = 'quotation-' . $this->id . '-' . time() . '.pdf';
        $path = 'quotations/' . $fileName;

        // 3. حفظ الملف في القرص الخاص (local)
        // القرص 'local' افتراضياً غير متاح للعامة
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = FacadesStorage::disk('local');
        $disk->put($path, $pdfContent);

        // 4. إرجاع المسار النسبي
        return $path;
    }
public function generatePdfContent(): string
{
    $quotation = $this;
    $currencyCode = $this->currency->code ?? 'USD';
    $locale = app()->getLocale();

    // 💡 تأكد من أن الـ Closure متاح (يمكنك تعريفهم كـ private methods أو تمريرهم)
    $formatCurrency = fn($amount) => number_format($amount, 2) . ' ' . $currencyCode;
    $translate = fn($key, $lang = null) => __("filament::admin.{$key}", [], $lang ?? $locale);

    // (يجب أن يتم جلب باقي المتغيرات المطلوبة في الـ PDF هنا)
    $customerVisibleCosts = $this->additionalCosts->where('show_to_customer', true)->sum('value');
    $finalGrandTotal = $this->total ?? $this->items->sum('final_price') + $customerVisibleCosts;


    $data = [
        'quotation' => $quotation,
        'currencyCode' => $currencyCode,
        'locale' => $locale,
        'formatCurrency' => $formatCurrency,
        'translate' => $translate,
        'customerVisibleCosts' => $customerVisibleCosts,
        'finalGrandTotal' => $finalGrandTotal,
    ];

    $config = [
        'default_font' => 'dejavusans',
        // ... (أي إعدادات أخرى تحتاجها)
    ];

    // 🚨 تأكد من أن مسار الـ View 'pdf.document' صحيح
    $pdf = PDF::loadView('pdf.document', $data, [], $config);

    return $pdf->output();
}
    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function additionalCosts()
    {
        return $this->hasMany(QuotationAdditionalCost::class);
    }
    // ... داخل Quotation class ...

/**
 * توليد الرابط العام لتحميل الـ PDF (يتم رفع الملف إلى التخزين العام).
 */
public function getPublicPdfDownloadLink(): string
{
    try {
        // 1. استدعاء دالة توليد المحتوى
        $pdfContent = $this->generatePdfContent();
    } catch (\Exception $e) {
        // إذا فشل توليد المحتوى لسبب ما (مثل خطأ في الـ Blade)
        \Illuminate\Support\Facades\Log::error("PDF generation failed for Quotation {$this->id}: " . $e->getMessage());
        return '#PDF_GENERATION_FAILED';
    }

    // 2. تحديد اسم الملف (باستخدام time() لضمان التفرد في كل مرة)
    $fileName = 'quotation-' . $this->id . '-' . time() . '.pdf';
    $path = 'quotations/' . $fileName;

    // 3. حفظ الملف في التخزين العام
    /** @var Filesystem $disk */
    $disk = FacadesStorage::disk('public');

    $disk->put($path, $pdfContent);

    // 4. إرجاع الرابط العام
    return $disk->url($path);
}


    public function getGrandTotalAttribute()
    {
        $itemsTotal = $this->items->sum('final_price');

        $extraCosts = $this->additionalCosts->sum('custom_value');
        $base = $this->additionalCosts()
            ->with(['additionalCost'])
            ->get()
            ->filter(fn($qac) => $qac->additionalCost?->is_standard)
            ->sum(fn($qac) => $qac->additionalCost?->value ?? 0);

        return $itemsTotal + $extraCosts + $base;
    }
    public function recalcTotal()
    {
        $items = $this->items()->get();
        $visibleCosts = $this->additionalCosts()->where('show_to_customer', true)->sum('custom_value');
        $hiddenCosts = $this->additionalCosts()->where('show_to_customer', false)->sum('custom_value');

        // أي standard costs من جدول additional_costs الرئيسي
        $base = $this->additionalCosts()
            ->with('additionalCost')
            ->get()
            ->filter(fn($qac) => $qac->additionalCost?->is_standard)
            ->sum(fn($qac) => $qac->additionalCost?->value ?? 0);

        if ($items->isNotEmpty()) {
            // مجموع البنود بدون التوزيع
            $itemsTotal = $items->sum(fn($item) => $item->selling_price * $item->quantity);

            // توزيع التكاليف المخفية على البنود
            foreach ($items as $item) {
                $ratio = $itemsTotal > 0 ? ($item->subtotal / $itemsTotal) : 0;
                $distributed = $ratio * $hiddenCosts;

                $taxAmount = 0;
                if (isset($item->tax)) {
                    // ✅ الضريبة دلوقتي على subtotal (سعر * كمية)
                    $taxAmount = ($item->subtotal * $item->tax->rate) / 100;
                }

                $item->final_price = $item->subtotal + $distributed + $taxAmount;
                $item->saveQuietly();
            }

            // الإجمالي النهائي
            $this->total = $items->sum('final_price') + $visibleCosts + $base;
        } else {
            // مافيش items → استخدم الـ manual_base_price و manual_margin_percentage
            $manualBase = $this->manual_base_price ?? 0;
            $manualMargin = $this->manual_margin_percentage ?? 0;
            $manualSelling = $manualBase + ($manualBase * $manualMargin / 100);

            $taxAmount = 0;
            if ($this->tax) {
                $taxAmount = ($manualSelling * $this->tax->rate) / 100;
            }

            // إضافة التكاليف المخفية والمرئية والـ base
            $this->total = $manualSelling + $hiddenCosts + $visibleCosts + $base + $taxAmount;
        }

        $this->saveQuietly();
    }



    public function mainCostsRelation()
    {
        return $this->hasMany(QuotationAdditionalCost::class)
            ->whereHas(['additionalCost'], fn($q) => $q->where('is_standard', true))
            ->with(['additionalCost']);
    }


    protected static function booted()
    {


        static::created(function ($quotation) {

            // 1. تحقق من وجود قيمة لـ rfq_id
            if ($quotation->rfq_id) {

                // 2. جلب سجل RFQ المرتبط
                $rfq = $quotation->rfq; // إذا كانت العلاقة RFQ معرفة في الموديل

                // أو استخدم find مباشرة:
                // $rfq = Rfq::find($quotation->rfq_id);

                if ($rfq && $rfq->status !== 'replied') {
                    // 3. تحديث الحالة
                    $rfq->status = 'replied';
                    $rfq->save();
                }
            }
        });
    }
}
