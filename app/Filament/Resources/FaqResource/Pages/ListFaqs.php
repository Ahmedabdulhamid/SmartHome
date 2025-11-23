<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\FaqPage;
use App\Models\Faq;
use Filament\Actions\DeleteAction;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;


    protected static string $view = 'filament.pages.faqs-page';

    protected static ?string $title = 'الأسئلة الشائعة';
    protected static ?string $navigationLabel = 'الأسئلة الشائعة';
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

     public function getViewData(): array
    {
        return [
            'faqs' => Faq::query()->get(),
        ];
    }
     protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),


        ];
    }
    public function deleteFaq($faqId)
    {
        $faq = Faq::find($faqId);

        // هنا نستخدم الإجراء الجاهز الذي يوفر التأكيد ومسار الحذف الصحيح
        DeleteAction::make('delete')
            ->record($faq)
            ->successNotificationTitle('تم حذف السؤال بنجاح!')
            ->authorize(true) // تأكد من وجود صلاحية الحذف
            ->call();

        // بعد الحذف، يجب تحديث الصفحة أو إشعارات Livewire
        $this->redirect(FaqResource::getUrl('index'));
    }
}
