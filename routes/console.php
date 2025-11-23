<?php


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // استيراد واجهة Schedule
use App\Console\Commands\ApplyScheduledDiscounts; // استيراد الكلاس الخاص بك

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// ----------------------------------------------
Schedule::command(ApplyScheduledDiscounts::class)
    ->everyTenMinutes()
    ->withoutOverlapping();
