<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdditionalCost;
class AdditionalCostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $costs = [
            [
                'name' => 'Shipping',
                'value' => 100.00,
                'show_to_customer' => true,
                'is_standard' => true,
            ],
            [
                'name' => 'Installation',
                'value' => 200.00,
                'show_to_customer' => true,
                'is_standard' => true,
            ],
            [
                'name' => 'Insurance',
                'value' => 50.00,
                'show_to_customer' => false,
                'is_standard' => true,
            ],
        ];

        foreach ($costs as $cost) {
            AdditionalCost::firstOrCreate(
                ['name' => $cost['name']],
                $cost
            );
        }
    }
}
