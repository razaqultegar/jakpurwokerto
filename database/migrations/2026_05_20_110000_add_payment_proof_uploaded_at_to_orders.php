<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
        });

        DB::table('orders')
            ->whereNotNull('payment_proof')
            ->whereNull('payment_proof_uploaded_at')
            ->update(['payment_proof_uploaded_at' => DB::raw('updated_at')]);
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_proof_uploaded_at');
        });
    }
};
