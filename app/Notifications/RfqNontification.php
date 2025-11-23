<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class RfqNontification extends Notification implements ShouldQueue
{
    use Queueable,SerializesModels;

    /**
     * Create a new notification instance.
     */
    public $rfq;
    public function __construct($rfq)
    {
        $this->rfq=$rfq;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('You have recieved a message from'.' '.$this->rfq->name)

            ->line('طلب عرض السعر');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
{
    return [
        'message' => 'New contact message from ' . $this->rfq->name,
        'rfq_id' => $this->rfq->id,
    ];
}
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
