<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    // هذه الدالة تنفذ بعد حفظ سجل الخدمة الجديد بنجاح
    protected function afterCreate(): void
    {
        /** @var Service $service */
        $service = $this->record;

        // استخراج بيانات الـ Repeater من حقل "feature_pivot_data"
        // هذا هو الحقل الذي تم إعداده يدوياً في ServiceResource
        $pivotData = $this->data['feature_pivot_data'] ?? [];

        // تحضير مصفوفة الربط (Attach Array)
        $featuresToAttach = [];
        foreach ($pivotData as $item) {
            // نستخدم feature_id كمفتاح ونمرر الحقول الإضافية (additional_cost, currency_id) كقيم للمحور
            // للتأكد من ربط البيانات الصحيحة بجدول feature_service
            $featuresToAttach[$item['feature_id']] = [
                'additional_cost' => $item['additional_cost'],
                'currency_id' => $item['currency_id'],
            ];
        }

        // ربط الميزات بالخدمة باستخدام دالة syncWithoutDetaching
        // هذه الدالة تضمن حفظ بيانات جدول الربط (feature_service)
        if (!empty($featuresToAttach)) {
            $service->features()->syncWithoutDetaching($featuresToAttach);
        }
    }
}
