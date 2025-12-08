<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
class SideBar extends Component
{
    public $invoicesCount;
    public $clientsCount;

    public function mount()
    {
        $this->invoicesCount=Invoice::count();
        $this->clientsCount=Client::count();


    }
    #[On('updateInvoiceCount')]
    public function getInvoicesCount()
    {
        $this->invoicesCount=Invoice::count();
    }
    #[On('updateClientCount')]
    public function getClientCount()
    {
         $this->clientsCount=Client::count();
    }
    public function render()
    {
        return view('livewire.side-bar');
    }
}
