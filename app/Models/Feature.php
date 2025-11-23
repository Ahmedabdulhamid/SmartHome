<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Feature extends Model
{
    use HasFactory, HasTranslations;

    // تحديد الحقول المترجمة (التي تم تعريفها كـ JSON في الـ Migration)
    public array $translatable = [
        'name',
        'description',
    ];


    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function services(): BelongsToMany
    {
        // استخدام withPivot() لجلب الحقول الإضافية من جدول الربط
        // (مثل additional_cost و currency_id)
        return $this->belongsToMany(Service::class, 'feature_service', 'feature_id', 'service_id')
                    ->withPivot(['additional_cost', 'currency_id', 'id']) // يجب إضافة id الجدول الوسيط
                    ->using(FeatureService::class); // استخدام نموذج جدول الربط المخصص
    }
}
