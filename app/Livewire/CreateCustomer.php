<?php

namespace App\Livewire;

use App\Http\Requests\ClientRequest;
use Livewire\Component;

class CreateCustomer extends Component
{
    public $name;
    public $email;
    public $phone;
    public $address;
    public function createCustomer()
{
    $request = new \App\Http\Requests\ClientRequest;

    $validatedData = $this->validate(
        $request->rules(),
        $request->messages()
    );

    $validatedData['user_id'] = auth()->guard('web')->id();

    \App\Models\Client::create($validatedData);

    $this->reset(['name', 'email', 'phone', 'address']);
    $this->dispatch('customerCreated', ['message' => 'Client created successfully!']);
    $this->dispatch('updateClientCount');
}

    public function render()
    {
        return view('livewire.create-customer');
    }
}
