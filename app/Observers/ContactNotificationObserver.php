<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\Contact;
use App\Notifications\ContactUsEmail;
use Filament\Notifications\Notification;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class ContactNotificationObserver
{
    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        $admin=Admin::where('email','admin@example.com')->first();
        if ($admin) {
            $admin->notify(new ContactUsEmail($contact));

           Notification::make()
                ->title('You Have a new message from' . ' ' . $contact->name)
                ->success()
                ->sendToDatabase($admin);
        }
        FacadesLog::info('A new contact message has been created: ' . $admin);
    }

    /**
     * Handle the Contact "updated" event.
     */
    public function updated(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "deleted" event.
     */
    public function deleted(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "restored" event.
     */
    public function restored(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "force deleted" event.
     */
    public function forceDeleted(Contact $contact): void
    {
        //
    }
}
