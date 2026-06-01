<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pickup_address')->nullable()->after('pickup_location');
            $table->string('pickup_contact_name', 100)->nullable()->after('pickup_address');
            $table->string('pickup_contact_phone', 30)->nullable()->after('pickup_contact_name');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_address', 'pickup_contact_name', 'pickup_contact_phone']);
        });
    }
};
