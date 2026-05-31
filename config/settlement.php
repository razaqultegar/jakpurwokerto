<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Batas Akhir Pelunasan
    |--------------------------------------------------------------------------
    | Tanggal tenggat pelunasan DP. Email pengingat dikirim pada H-7, H-5, H-3,
    | dan Hari-H (sesuai daftar offset di bawah).
    */
    'deadline' => env('SETTLEMENT_DEADLINE', '2026-06-20'),

    /*
    | Jumlah hari sebelum tenggat untuk mengirim pengingat. 0 = tepat Hari-H.
    */
    'reminder_offsets' => [7, 5, 3, 0],

    /*
    | Jam (zona waktu aplikasi) saat scheduler menjalankan pengiriman pengingat.
    */
    'reminder_time' => env('SETTLEMENT_REMINDER_TIME', '08:00'),
];
