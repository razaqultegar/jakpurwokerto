<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('dp_settlement_uploaded_at')->nullable()->after('dp_settlement_proof');
            $table->timestamp('dp_settlement_verified_at')->nullable()->after('dp_settlement_uploaded_at');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['dp_settlement_uploaded_at', 'dp_settlement_verified_at']);
        });
    }
};
