<?php

namespace App\Livewire;

use App\Models\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

class HeadNave extends Component
{
    // الخاصية التي تحمل عدد عناصر السلة
    public $cartCount = 0;

    /**
     * تهيئة عدد عناصر السلة عند تحميل المكون.
     * يتم استرجاع السلة بناءً على معرف المستخدم أو الجلسة.
     */

     #[On('cart_count_updated')]
    public function mount()
    {
        $userId = auth()->guard('web')->id();
        $sessionId = session()->getId();

        // إنشاء استعلام مرن (Flexible Query)
        $cartQuery = Cart::query();

        if ($userId) {
            // الأولوية للمستخدم المسجل دخوليًا
            $cartQuery->where('user_id', $userId);
        } else {
            // للضيوف، نعتمد على Session ID
            $cartQuery->where('session_id', $sessionId);
        }

        // الحصول على كائن السلة الوحيد
        $cart = $cartQuery->first();

        // إذا وُجدت السلة، نقوم بعدّ العناصر التابعة لها
        if ($cart) {
            // يجب التأكد من وجود علاقة 'items' في موديل Cart
            // تشير إلى موديل CartItem (مثال: $this->hasMany(CartItem::class))
            $this->cartCount = $cart->items()->count();
        } else {
            $this->cartCount = 0;
        }
    }

    /**
     * تحديث عدد عناصر السلة عند تلقي حدث 'cart_count_updated'
     * هذا هو الأسلوب الأمثل للتحديث في Livewire (Events)
     */
    #[On('cart_count_updated')]
    public function updateCartCount($count)
    {
        // يجب أن تكون القيمة التي تأتي من الحدث هي العدد الجديد
        $this->cartCount = (int) $count;
    }

    public function render()
    {
        return view('livewire.head-nave');
    }
}
