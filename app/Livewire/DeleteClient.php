<?php

namespace App\Livewire;

use Livewire\Component;

class DeleteClient extends Component
{
    public $client;
    public $confirmingClientDeletion = false;
    public function mount($client)
    {
        $this->client = $client;
    }
    public function deleteClient()
    {
        $this->client->delete();
        session()->flash('message', 'Client deleted successfully.');
        return redirect()->route('clients.index');
    }
    public function render()
    {
        return view('livewire.delete-client');
    }
}
