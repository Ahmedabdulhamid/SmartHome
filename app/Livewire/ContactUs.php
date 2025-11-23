<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Notifications\ContactUsEmail;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ContactUs extends Component
{
    public $name, $email, $subject, $message, $status;
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ];
    }
    public function submit()
    {
        $this->validate();
        $contact = \App\Models\Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'new',
        ]);
        $admins = Admin::all();
        foreach ($admins as $admin) {
            if ($admin->hasRole('Super Admin')) {
                $admin->notify(new ContactUsEmail($contact));

                Notification::make()
                    ->title('You Have a new message from' . ' ' . $contact->name)
                    ->success()
                    ->sendToDatabase($admin);
            }
        }


        $this->dispatch('success', __('web.contact_success'));

        $this->reset(['name', 'email', 'subject', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-us');
    }
}
