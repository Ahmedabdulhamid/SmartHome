<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureService extends Pivot
{
    // تحديد اسم الجدول الوسيط يدوياً (للتأكد فقط)
    protected $table = 'feature_service';

    // يجب إضافة الحقول المتاحة في جدول الربط هنا
    protected $fillable = [
        'service_id',
        'feature_id',
        'additional_cost',
        'currency_id',
    ];

    // إضافة Casts للمفاتيح الأجنبية للتأكد من قراءة البيانات كأرقام
    protected $casts = [
        'service_id' => 'integer',
        'feature_id' => 'integer',
        'currency_id' => 'integer',
        'additional_cost' => 'decimal:2', // التأكد من نوع الحقل إذا كان رقم عشري
    ];

    // =======================================================
    // العلاقات: هذه هي الدوال التي يبحث عنها Filament
    // =======================================================

    /**
     * العلاقة لربط الصف بالعملة.
     */
    public function currency(): BelongsTo
    {
        // يربط الصف بالعملة باستخدام currency_id
        return $this->belongsTo(Currency::class);
    }

    /**
     * العلاقة لربط الصف بالميزة.
     */
    public function feature(): BelongsTo
    {
        // يربط الصف بالميزة باستخدام feature_id
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    // يمكنك إضافة علاقة الخدمة هنا للمرجعية، رغم أنها غير مطلوبة للـ Repeater
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
