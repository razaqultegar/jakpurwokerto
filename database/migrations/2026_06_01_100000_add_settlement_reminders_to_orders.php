<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Daftar tahap reminder pelunasan yang sudah terkirim, mis. ["h-7","h-5"].
            $table->json('dp_settlement_reminders')->nullable()->after('dp_settlement_verified_at');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('dp_settlement_reminders');
        });
    }
};
