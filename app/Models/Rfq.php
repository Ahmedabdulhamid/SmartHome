<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Propaganistas\LaravelPhone\PhoneNumber;
class Rfq extends Model
{
    protected $guarded = ['id'];
 protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $this->formatPhoneForDatabase($value),
        );
    }

    private function formatPhoneForDatabase(?string $value): string
    {
        $value = trim($value ?? '');
        if (empty($value)) {
            return '';
        }

        try {
            // 🚨 استخدام كلاس Phone للحزمة للتنسيق
            $phoneNumber = new PhoneNumber($value, 'EG'); // 'EG' هو رمز المنطقة الافتراضي

            // نطلب تنسيقه كـ E.164 (الذي يضمن وجود + في البداية)
            return $phoneNumber->formatE164();

        } catch (\Exception $e) {
            // إذا كان الرقم غير صالح (مثل نص أو رقم ناقص)، نتركه كما هو
            return $value;
        }
    }

    public function quotations()

    {
        return $this->hasMany(Quotation::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function items()
    {
        return $this->hasMany(RfqItem::class);
    }
}
