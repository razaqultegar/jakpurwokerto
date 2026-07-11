<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan sinkronisasi status anggota setiap hari pukul 00:30
Schedule::command('members:sync-status')->dailyAt('00:30')
    ->name('members:sync-status')
    ->withoutOverlapping();
