<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Support\FrontendCache;
use Livewire\Attributes\On;
use Livewire\Component;

class HeadNave extends Component
{
    public $cartCount = 0;

    #[On('cart_count_updated')]
    public function mount()
    {
        $userId = auth()->guard('web')->id();
        $sessionId = session()->getId();

        $context = $userId
            ? ['user_id' => $userId]
            : ['session_id' => $sessionId];

        $this->cartCount = FrontendCache::remember('header_cart_count', $context, 60, function () use ($userId, $sessionId) {
            return Cart::query()
                ->when($userId, function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }, function ($query) use ($sessionId) {
                    $query->where('session_id', $sessionId);
                })
                ->withCount('items')
                ->value('items_count') ?? 0;
        });
    }

    #[On('cart_count_updated')]
    public function updateCartCount($count)
    {
        $this->cartCount = (int) $count;
    }

    public function render()
    {
        return view('livewire.head-nave');
    }
}
