<?php

namespace App\Livewire;

use App\Http\Requests\Livewire\ContactUsRequest;
use App\Services\Frontend\ContactService;
use App\Support\Livewire\ValidatesWithFormRequest;
use Livewire\Component;

class ContactUs extends Component
{
    use ValidatesWithFormRequest;

    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;
     protected function rules()
    {
         return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ];
    }
    public function submit(): void
    {
        $validated = $this->validate();

        app(ContactService::class)->submit($validated);

        $this->dispatch('success', __('web.contact_success'));
        $this->reset([]);
    }

    public function render()
    {
        return view('livewire.contact-us');
    }
}
