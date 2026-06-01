<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alamat & kontak pengambilan kini disimpan per-pesanan (kolom di tabel orders),
     * sehingga pengaturan global per kota tidak lagi diperlukan. Sisakan key + name
     * sebagai katalog kota untuk dropdown checkout & label.
     */
    public function up()
    {
        Schema::table('pickup_locations', function (Blueprint $table) {
            $table->dropColumn(['address', 'contact_name', 'contact_phone']);
        });
    }

    public function down()
    {
        Schema::table('pickup_locations', function (Blueprint $table) {
            $table->string('address')->nullable()->after('name');
            $table->string('contact_name', 100)->nullable()->after('address');
            $table->string('contact_phone', 30)->nullable()->after('contact_name');
        });
    }
};
