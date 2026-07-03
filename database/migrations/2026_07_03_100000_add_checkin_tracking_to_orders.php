<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('checkin_code', 12)->nullable()->unique()->after('status');
            $table->timestamp('checked_in_at')->nullable()->after('checkin_code');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['checkin_code', 'checked_in_at']);
        });
    }
};
