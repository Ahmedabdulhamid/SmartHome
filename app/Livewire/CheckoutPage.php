<?php

namespace App\Livewire;

use App\Http\Requests\Livewire\CheckoutSubmitRequest;
use App\Services\Frontend\CartService;
use App\Services\Frontend\CheckoutService;
use App\Support\Livewire\ValidatesWithFormRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CheckoutPage extends Component
{
    use ValidatesWithFormRequest;

    public $cartItems = [];
    public $paymMethods = [];
    public $f_name;
    public $l_name;
    public $govoernorateId;
    public $cityId;
    public $email;
    public $phone;
    public $address;
    public $shipping_type;
    public $paym_method;
    public $zip_code;
    public $shipping_price = 0;
    public $estimated_days;

    public function mount(): void
    {
        $this->cartItems = app(CartService::class)->getCartSnapshot()['cart'];
        $this->paymMethods = app(CheckoutService::class)->getCheckoutData(null, null, null)['paymMethods'];
    }
protected function rules()
{
    return [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'govoernorateId' => ['required', 'exists:governorates,id'],
            'cityId' => ['required', 'exists:cities,id'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'phone:EG,SA,AE,QA,KW,BH,OM,JO,LB,MA,DZ,TN,LY,YE,IQ,SD,US'],
            'address' => ['required', 'string'],
            'shipping_type' => ['required', 'string'],
            'paym_method' => ['required', 'exists:paym_methods,id'],
            'zip_code' => ['required', 'string'],
        ];
}

    public function submit(): void
    {

      $validated = $this->validate();

        $result = app(CheckoutService::class)->submit($validated, (float) $this->shipping_price);

        $this->dispatch('order_created', __('web.order_creared_successfully'));
        $this->dispatch('cart_count_updated', count: $result['cart_count']);

        $this->reset([
            'f_name',
            'l_name',
            'email',
            'phone',
            'address',
            'govoernorateId',
            'cityId',
            'shipping_type',
            'paym_method',
            'zip_code',
        ]);

        $this->shipping_price = 0;
        $this->estimated_days = null;
        $this->cartItems = null;
    }

    public function render()
    {
        $data = app(CheckoutService::class)->getCheckoutData(
            $this->govoernorateId ? (int) $this->govoernorateId : null,
            $this->cityId ? (int) $this->cityId : null,
            $this->shipping_type ?: null,
        );

        $shippingPrice = $data['shippingPrice'];
        $this->paymMethods = $data['paymMethods'];

        if ($shippingPrice) {
            $this->shipping_price = (float) $shippingPrice->price;
            $this->estimated_days = $shippingPrice->estimated_days;
        } else {
            $this->shipping_price = 0;
            $this->estimated_days = null;
        }

        return view('livewire.checkout-page', [
            'governorates' => $data['governorates'],
            'cities' => $data['cities'],
            'shippingPrice' => $shippingPrice,
        ]);
    }
}
