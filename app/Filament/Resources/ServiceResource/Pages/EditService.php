<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // =======================================================
    // منطق تحميل بيانات الـ Repeater (التحويل من علاقة إلى مصفوفة Repeater)
    // =======================================================

    // هذه الدالة يتم استدعاؤها قبل ملء النموذج (Form) ببيانات السجل
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Service $service */
        $service = $this->getRecord();

        // جلب بيانات الميزات المحورية (features) الموجودة وتحويلها إلى الهيكل الذي يتوقعه الـ Repeater
        $repeaterData = $service->features->map(function ($feature) {
            return [
                'feature_id' => $feature->id, // المفتاح الأساسي للميزة
                'additional_cost' => $feature->pivot->additional_cost, // البيانات المحورية
                'currency_id' => $feature->pivot->currency_id, // البيانات المحورية
            ];
        })->toArray();

        // إدخال البيانات المُهيكلة في حقل "feature_pivot_data" في النموذج
        $data['feature_pivot_data'] = $repeaterData;

        return $data;
    }


    // =======================================================
    // منطق حفظ بيانات الـ Repeater (التحديث)
    // =======================================================

    // هذه الدالة تنفذ بعد حفظ سجل الخدمة (التحديث) بنجاح
    protected function afterSave(): void
    {
        /** @var Service $service */
        $service = $this->getRecord();

        // استخراج بيانات الـ Repeater المُعدلة من حقل "feature_pivot_data"
        $pivotData = $this->data['feature_pivot_data'] ?? [];

        // تحضير مصفوفة المزامنة (Sync Array)
        $featuresToSync = [];
        foreach ($pivotData as $item) {
            // نستخدم feature_id كمفتاح ونمرر الحقول الإضافية كقيم للمحور
            $featuresToSync[$item['feature_id']] = [
                'additional_cost' => $item['additional_cost'],
                'currency_id' => $item['currency_id'],
            ];
        }

        // مزامنة الميزات مع الخدمة. دالة sync تحذف العلاقات القديمة غير الموجودة وتضيف/تحدث الجديدة
        $service->features()->sync($featuresToSync);
    }
}
