<?php

namespace App\Http\Requests\Livewire;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
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
}
