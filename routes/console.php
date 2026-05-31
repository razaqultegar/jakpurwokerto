<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat pelunasan DP (H-7, H-5, H-3, Hari-H sebelum tenggat).
Schedule::command('orders:settlement-reminders')
    ->dailyAt(config('settlement.reminder_time', '08:00'))
    ->withoutOverlapping();
