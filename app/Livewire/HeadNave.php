<?php

namespace App\Livewire;

use App\Models\CartItem;
use Livewire\Attributes\On;
use Livewire\Component;

class HeadNave extends Component
{
    public $cartCount;
    public function mount()
    {
       $this->cartCount=CartItem::count();
    }
    #[On('cart_count_updated')]
    public function updateCartCount($count)
    {

        $this->cartCount = $count;

    }

    public function render()
    {
        return view('livewire.head-nave');
    }
}
