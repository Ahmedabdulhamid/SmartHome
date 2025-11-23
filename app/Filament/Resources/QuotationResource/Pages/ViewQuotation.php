<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use Filament\Resources\Pages\Page;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use App\Mail\QuotationMail;
use App\Models\Setting;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class ViewQuotation extends Page
{
    protected static string $resource = QuotationResource::class;
    protected static string $view = 'filament.resources.quotation-resource.pages.view-quotation';

    public $record;
    public $quotation;
    public $setting;
    public function mount($record): void
    {
        $this->record = $record;
        $this->quotation = \App\Models\Quotation::with([
            'items.product',
            'template',
            'items.variant.attributeValuesPivot.attribute',
            'items.variant.attributeValuesPivot.attributeValue',

            'additionalCosts.additionalCost',
            'currency',
            'rfq'
        ])->find($record);
        $this->setting=Setting::first();

    }





    public function generatePdf()
    {


        $quotation = $this->quotation;

        $currencyCode = $quotation->currency->code ?? 'USD';
        $locale = app()->getLocale();


        $formatCurrency = function ($amount) use ($currencyCode) {
            // تأكد من استخدام دالة تنسيق العملة الفعلية
            return number_format($amount, 2) . ' ' . $currencyCode;
        };

        $translate = function ($key, $lang = null) use ($locale) {
            // التأكد من استخدام مفتاح الترجمة الصحيح
            return __("filament::admin.{$key}", [], $lang ?? $locale);
        };


        $customerVisibleCosts = $quotation->additionalCosts->where('show_to_customer', true)->sum('value');
        $finalGrandTotal = $quotation->total ?? $quotation->items->sum('final_price') + $customerVisibleCosts;


        $data = [
            'quotation' => $quotation,
            'currencyCode' => $currencyCode,
            'locale' => $locale,
            'formatCurrency' => $formatCurrency,
            'translate' => $translate,
            'customerVisibleCosts' => $customerVisibleCosts,
            'finalGrandTotal' => $finalGrandTotal,
        ];


        $filename = 'Quotation-' . $quotation->id . '.pdf';

        $config = [
            'default_font' => 'dejavusans',
        ];


        $pdf = PDF::loadView('pdf.document', $data, [], $config);


        $content = $pdf->output();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    protected function getHeaderActions(): array
    {
        $quotation = $this->quotation;
        $customerEmail = $quotation->rfq->email ?? null;
        $customerPhone = $quotation->rfq->phone ?? null;
        $quotationId = $quotation->id;

        // 💡 1. توليد الرابط الآمن والموقع المؤقت (Secure Signed URL)
        // هذا الرابط لن يعمل إلا إذا كان التوقيع صحيحًا ولم تنتهِ صلاحيته (7 أيام).
        $secureSignedUrl = URL::temporarySignedRoute(
            'quotation.signed.download', // اسم المسار الآمن الذي أنشأناه
            now()->addDays(7),           // الصلاحية: 7 أيام
            ['quotation' => $quotationId]
        );

        // 2. استخدام الرابط الآمن في رسالة واتساب
        $whatsappMessageBody = "مرحباً، إليك عرض السعر رقم: {$quotationId}."
            . "\n\n"
            . "يمكنك مراجعة المستند كاملاً عبر هذا الرابط (صالح لمدة 7 أيام): " . $secureSignedUrl;


        return [

            // 1. زر المشاركة عبر واتساب (WhatsApp)
            Action::make('share_via_whatsapp')
                ->label(__('filament::admin.share_with_whatsapp'))
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('success')
                ->url(function () use ($customerPhone, $whatsappMessageBody, $quotation) {

                    if (empty($customerPhone)) {
                        \Filament\Notifications\Notification::make()->title('خطأ')->body('يرجى تسجيل رقم هاتف العميل للمشاركة عبر واتساب.')->danger()->send();
                        return '#';
                    }

                    // 🚨 المنطق التالي لتنسيق رقم الهاتف يبقى كما هو
                    $appLocale = app()->getLocale();
                    $localeCountryCode = null;

                    if (str_contains($appLocale, '_')) {
                        $localeCountryCode = strtoupper(substr($appLocale, -2));
                    }
                    elseif (strlen($appLocale) === 2) {
                        if ($appLocale === 'ar') {
                            $localeCountryCode = 'EG';
                        } elseif ($appLocale === 'en') {
                            $localeCountryCode = 'US';
                        }
                    }

                    $fallbackRegions = [
                        'SA', 'EG', 'AE', 'QA', 'KW', 'BH', 'OM', 'JO', 'LB', 'MA', 'DZ', 'TN', 'LY', 'YE', 'IQ', 'SD', 'US',
                    ];

                    $regionsToTry = $fallbackRegions;

                    if ($localeCountryCode && ctype_alpha($localeCountryCode) && strlen($localeCountryCode) === 2) {
                        $regionsToTry = array_diff($fallbackRegions, [$localeCountryCode]);
                        array_unshift($regionsToTry, $localeCountryCode);
                    }

                    $formattedPhone = '';

                    foreach ($regionsToTry as $regionCode) {
                        try {
                            $phoneNumberObject = new PhoneNumber($customerPhone, $regionCode);

                            if ($phoneNumberObject->isValid()) {
                                $formattedPhone = ltrim($phoneNumberObject->formatE164(), '+');
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if (empty($formattedPhone)) {
                        \Filament\Notifications\Notification::make()
                            ->title('فشل التنسيق الدولي')
                            ->body("الرقم المدخل غير صالح لأي من المناطق المحددة. يرجى إدخاله بالصيغة الدولية (+).")
                            ->danger()->send();
                        return '#';
                    }
                    // نهاية منطق تنسيق رقم الهاتف 🚨

                    return 'https://wa.me/' . $formattedPhone . '?text=' . urlencode($whatsappMessageBody);
                })
                ->openUrlInNewTab()
                ->visible(fn() => !empty($customerPhone)),


            // 2. زر الإرسال عبر البريد الإلكتروني (Server-Side Email)
            Action::make('send_via_email')
                ->label(__('filament::admin.share_with_gemail'))
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->action(function () use ($customerEmail, $quotation, $secureSignedUrl) {

                    if (empty($customerEmail)) {
                        \Filament\Notifications\Notification::make()->title('خطأ')->body('لا يوجد بريد إلكتروني مسجل للعميل.')->danger()->send();
                        return;
                    }

                    try {

                        Mail::to($customerEmail)
                            ->locale(app()->getLocale())
                            ->send(new QuotationMail($quotation, $secureSignedUrl)); // تم إزالة الـ null الوسيط الثاني

                        \Filament\Notifications\Notification::make()->title('تم إرسال عرض السعر بنجاح.')->success()->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('فشل إرسال البريد')
                            ->body('حدث خطأ: يرجى مراجعة إعدادات SMTP. الخطأ: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn() => !empty($customerEmail)),


        ];
    }
}
