<?php
namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ContactReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $replyMessage;
    // ⭐️ إضافة خاصية لتخزين لغة المستلم
    public $locale;

    // ⭐️ استقبال متغير اللغة في الدالة البانية
    public function __construct($replyMessage, string $locale = 'ar')
    {
        $this->replyMessage = $replyMessage;
        $this->locale = $locale;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // ⭐️ تعيين اللغة
        $this->locale($this->locale);

        // ⭐️ جلب الإعدادات والـ URL الخاص بالشعار
        $setting=Setting::first();
        // ⚠️ يجب التأكد من وجود عمود 'site_logo' في جدول الإعدادات
        $logoPath = $setting->site_logo ?? 'img/fallback-logo.png';
        $logoUrl = url('public/storage/'.$logoPath);

        return $this->subject(__('email.contact_reply_subject', [], $this->locale))
                    // ⭐️ نستخدم 'view' مع القالب المعدل
                    ->view('emails.contact-reply', [
                        'replyMessage' => $this->replyMessage,
                        'logoUrl' => $logoUrl,
                    ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
