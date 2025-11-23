<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// ⚠️ تأكد من استيراد نموذج عرض السعر الخاص بك
// مثال: use App\Models\Quotation;

class ReserveQuotationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // ⭐️ 1. تعريف خاصية عامة لتخزين السجل
   public $quotation;
    // ⭐️ إضافة خاصية لتخزين لغة المستلم
    public $locale;

    public function __construct($quotation, $locale = 'en') // ⭐️ استقبال متغير اللغة وتعيين الافتراضي 'en'
    {
        $this->quotation = $quotation;
        $this->locale = $locale; // ⭐️ تخزين اللغة
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // ⭐️ 4. تعديل الموضوع ليشمل رقم عرض السعر
        return new Envelope(
            subject: 'تأكيد حجز عرض السعر رقم: ' . $this->quotation->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.reserve-quotation',
            // ⭐️ 5. تمرير البيانات إلى ملف الـ View (Markdown)
            with: [
                'quotation' => $this->quotation,
            ],
        );
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
