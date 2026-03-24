<?php

namespace Database\Seeders;

use App\Models\PaymMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            'name'=>"Cashe on Delivery"
        ];
        PaymMethod::create($data);
    }
}
