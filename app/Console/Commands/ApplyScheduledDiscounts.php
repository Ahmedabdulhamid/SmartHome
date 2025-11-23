<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplyScheduledDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discounts:toggle';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activates and deactivates scheduled product discounts based on start_at and ends_at.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Starting discount toggle process at: {$now}");

        // 1. **إلغاء الخصومات المنتهية (Deactivation)** 🚫
        // نبحث عن المنتجات التي كانت نشطة والآن تجاوزت تاريخ الانتهاء (ends_at)
        $deactivatedCount = DB::table('products')
            ->where('has_discount', true)
            ->where('ends_at', '<', $now)
            ->update([
                'has_discount' => false,
            ]);

        $this->info("Deactivated discounts on {$deactivatedCount} products.");

        // 2. **تفعيل الخصومات الجديدة/المجدولة (Activation)** ✅
        // نبحث عن المنتجات التي لم تُفعّل بعد وتاريخ بدايتها (start_at) قد حان (أو مر) ولم ينتهِ بعد
        $activatedCount = DB::table('products')
            ->where('has_discount', false)
            ->where('discount_percentage', '>', 0) // يجب أن يكون هناك نسبة خصم مُدخلة
            ->where('start_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->update([
                'has_discount' => true,
            ]);

        $this->info("Activated discounts on {$activatedCount} products.");

        $this->info('Discount toggle process completed.');
        return 0;
    }
}
