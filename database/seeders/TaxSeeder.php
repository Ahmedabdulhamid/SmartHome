<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       /* DB::table('taxes')->insert([
    ['name' => 'ضريبة القيمة المضافة', 'type' => 'vat', 'rate' => 14.00],
    ['name' => 'ضريبة المبيعات', 'type' => 'sales', 'rate' => 10.00],
    ['name' => 'ضريبة الاستقطاع', 'type' => 'withholding', 'rate' => 5.00],
]);*/
    }
}
