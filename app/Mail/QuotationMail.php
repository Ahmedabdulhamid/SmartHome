<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content; // لا نحتاجها في حال استخدام build()
use Illuminate\Mail\Mailables\Envelope; // لا نحتاجها في حال استخدام build()
use Illuminate\Queue\SerializesModels;
use App\Models\Quotation;

class QuotationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $quotation;
    public $secureUrl;
    // ⭐️ خاصية جديدة لتخزين اللغة
    public $locale;

    /**
     * Create a new message instance.
     */
    public function __construct(Quotation $quotation, string $secureUrl, string $locale = 'en') // ⭐️ استقبال متغير اللغة
    {
        $this->quotation = $quotation;
        $this->secureUrl = $secureUrl;
        $this->locale = $locale; // ⭐️ تخزين اللغة
    }

    // 💡 سنقوم بحذف دالتي envelope() و content() واستخدام build() لتطبيق اللغة
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // 1. تطبيق اللغة على المراسلة بالكامل (Mailable)
        return $this->locale($this->locale)
                    // 2. تعيين الموضوع باستخدام الترجمة المناسبة للغة المستلم
                    ->subject(__('email.quotation_subject', ['id' => $this->quotation->id], $this->locale))
                    // 3. تحديد القالب وتمرير البيانات
                    ->markdown('email.quotation-mail', [
                        'quotation' => $this->quotation,
                        'secureUrl' => $this->secureUrl,
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
