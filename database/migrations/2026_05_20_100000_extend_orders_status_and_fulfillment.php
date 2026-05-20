<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','paid','verified','completed','cancelled') NOT NULL DEFAULT 'pending'");
        DB::table('orders')->where('status', 'paid')->update(['status' => 'verified']);
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','verified','completed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_tracking', 100)->nullable()->after('payment_proof');
            $table->string('dp_settlement_proof', 255)->nullable()->after('shipping_tracking');
            $table->timestamp('verified_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('verified_at');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_tracking', 'dp_settlement_proof', 'verified_at', 'completed_at']);
        });

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','verified','completed','cancelled','paid') NOT NULL DEFAULT 'pending'");
        DB::table('orders')->where('status', 'verified')->update(['status' => 'paid']);
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
