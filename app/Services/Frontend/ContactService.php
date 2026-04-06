<?php

namespace App\Services\Frontend;

use App\Models\Admin;
use App\Notifications\ContactUsEmail;
use App\Repositories\Frontend\ContactRepository;
use Filament\Notifications\Notification;

class ContactService
{
    public function __construct(
        private readonly ContactRepository $contacts,
    ) {}

    public function submit(array $validated)
    {
        $contact = $this->contacts->create([
            ...$validated,
            'status' => 'new',
        ]);

        $admins = Admin::query()->get();

        foreach ($admins as $admin) {
            if (! $admin->hasRole('Super Admin')) {
                continue;
            }

            $admin->notify(new ContactUsEmail($contact));

            Notification::make()
                ->title('You Have a new message from ' . $contact->name)
                ->success()
                ->sendToDatabase($admin);
        }

        return $contact;
    }
}
