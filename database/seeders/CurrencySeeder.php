<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'precision' => 2,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => true,
                'active' => true,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'precision' => 2,
                'decimal_mark' => ',',
                'thousands_separator' => '.',
                'symbol_first' => true,
                'active' => true,
            ],
            [
                'code' => 'EGP',
                'name' => 'Egyptian Pound',
                'symbol' => 'ج.م',
                'precision' => 2,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'precision' => 2,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => true,
                'active' => true,
            ],
            [
                'code' => 'SAR',
                'name' => 'Saudi Riyal',
                'symbol' => '﷼',
                'precision' => 2,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],
        [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'symbol' => 'د.إ',
                'precision' => 2,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],

            // الكويت
            [
                'code' => 'KWD',
                'name' => 'Kuwaiti Dinar',
                'symbol' => 'د.ك',
                'precision' => 3, // الكويت بتستخدم 3 أرقام عشرية
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],

            // قطر
            [
                'code' => 'QAR',
                'name' => 'Qatari Riyal',
                'symbol' => 'ر.ق',
                'precision' => 2,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],

            // عمان
            [
                'code' => 'OMR',
                'name' => 'Omani Rial',
                'symbol' => 'ر.ع.',
                'precision' => 3, // عمان كمان بتستخدم 3 أرقام عشرية
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],

            // البحرين
            [
                'code' => 'BHD',
                'name' => 'Bahraini Dinar',
                'symbol' => 'ب.د',
                'precision' => 3,
                'decimal_mark' => '.',
                'thousands_separator' => ',',
                'symbol_first' => false,
                'active' => true,
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
