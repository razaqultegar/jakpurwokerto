<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','verified','paid','shipped','completed','cancelled') NOT NULL DEFAULT 'pending'");

        // Order full payment yang sudah 'verified' = sudah lunas → naikkan ke 'paid'.
        DB::table('orders')
            ->where('status', 'verified')
            ->where('payment_type', 'full')
            ->update(['status' => 'paid']);
    }

    public function down()
    {
        // Petakan status baru kembali ke skema lama sebelum mengecilkan ENUM.
        DB::table('orders')->whereIn('status', ['paid', 'shipped'])->update(['status' => 'verified']);

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','verified','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
