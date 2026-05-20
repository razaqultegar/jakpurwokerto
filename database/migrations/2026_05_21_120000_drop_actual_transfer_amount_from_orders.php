<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'actual_transfer_amount')) {
                $table->dropColumn('actual_transfer_amount');
            }
            if (Schema::hasColumn('orders', 'transfer_note')) {
                $table->dropColumn('transfer_note');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('actual_transfer_amount')->nullable()->after('amount_due');
            $table->text('transfer_note')->nullable()->after('actual_transfer_amount');
        });
    }
};
