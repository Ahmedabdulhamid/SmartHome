<?php

namespace App\Livewire;

use App\Events\OrderCreated;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\PaymMethod;
use App\Models\ShippingPrice;
use Filament\Notifications\Notification;
use Livewire\Component;

class CheckoutPage extends Component
{
    public $cartItems = [];
    public $paymMethods = [];
    public $f_name, $l_name, $govoernorateId, $cityId, $email, $phone, $address, $shipping_type, $paym_method, $zip_code, $shipping_price = 0,$estimated_days;
    public function mount()
    {
        $userId = auth()->guard('web')->user()?->id;

        if (!$userId) {
            $oldSessionId = session()->getId();
            $this->cartItems = Cart::where('session_id', $oldSessionId)->with('items')->first();
        } else {
            $this->cartItems = Cart::where('user_id', $userId)->with('items')->first();
        }
        $this->paymMethods = PaymMethod::all();
    }
    public function submit()
    {

        $data = $this->validate([
            'f_name' => "required|max:255",
            "l_name" => "required|max:255",
            "govoernorateId" => "required|exists:governorates,id",
            'cityId' => "required|exists:cities,id",
            'email' => "required|email",
            'phone' => ['required', 'phone:EG,SA,AE,QA,KW,BH,OM,JO,LB,MA,DZ,TN,LY,YE,IQ,SD,US'],
            'address' => 'required',
            'shipping_type' => "required",
            "paym_method" => "required",
            "zip_code" => "required"

        ]);
        $order = Order::create([
            'user_id' => auth()->guard('web')->user()?->id,
            'payment_method_id' => $this->paym_method,
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'governorate_id' => $this->govoernorateId,
            'city_id' => $this->cityId,
            'status' => "pending",
            'shipping_price' => $this->shipping_price,
            "currency_id" => $this->cartItems->items()->first()->currency->id,
            'total_amount'=>$this->calculateTotalPrice(),
            "zip_code"=>$this->zip_code

        ]);
        foreach ($this->cartItems->items as $item) {
            $order->items()->create([
                'order_id' => $order->id,
                'product_id' => $item->pivot->product_id,
                'product_variant_id' => $item->pivot->product_variant_id,
                'price' => $item->pivot->price,
                'quantity' => $item->pivot->quantity,
                'total' => $item->pivot->quantity * $item->pivot->price,

                'currency_id' => $item->currency->id


            ]);
        }
        $this->dispatch('order_created',__('web.order_creared_successfully'));
        $this->reset([
            'f_name', 'l_name', 'email', 'phone', 'address',
            'govoernorateId', 'cityId', 'shipping_type', 'paym_method', 'zip_code'
        ]);
        $this->cartItems->delete();
        $cartCount=$this->cartItems->items()->count();
        $this->cartItems = null;
        $admins = Admin::all();
        foreach ($admins as $admin) {

                event(new OrderCreated($order,$admin));

                Notification::make()
                ->title('New Order Created')
                ->broadcast($admin)
                ->sendToDatabase($admin);



        }

        $this->dispatch('cart_count_updated', count:$cartCount);
    }
    public function calculateTotalPrice()
    {
        $subtotal=0;
        foreach ($this->cartItems->items as $item) {
           $subtotal+=$item->pivot->price+$item->pivot->quantity ;
        }
        return $subtotal + $this->shipping_price;

    }


    // في كلاس Livewire
    public function render()
    {
        // ... (تحديث المدن كما هو صحيح)

        $governorates = Governorate::all();
        $cites = $this->govoernorateId ? City::where('governorate_id', $this->govoernorateId)->get() : [];

        // نحدد القيمة الافتراضية لـ $shippingPrice كـ null
        $shippingPrice = null;

        if ($this->govoernorateId && $this->cityId && isset($this->shipping_type)) {
            // إذا توفرت كافة المدخلات، نبحث عن سعر الشحن
            $shippingPrice = ShippingPrice::where('governorate_id', $this->govoernorateId)
                ->where('city_id', $this->cityId)
                ->where('shipping_type', $this->shipping_type)
                ->with('currency')
                ->first();
        }
        if (isset($shippingPrice)) {
            $this->shipping_price = $shippingPrice->price;
            $this->estimated_days=$shippingPrice->estimated_days;
        }

        // ... (تكملة دالة render)
        return view('livewire.checkout-page', [
            'governorates' => $governorates,
            'cities' => $cites,
            'shippingPrice' => $shippingPrice
        ]);
    }
}
