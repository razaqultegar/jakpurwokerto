<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pickup_locations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name', 100);
            $table->string('address')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('pickup_locations')->insert([
            [
                'key' => 'purwokerto',
                'name' => 'Purwokerto',
                'address' => 'Jl. Jenderal Soedirman No.— , Purwokerto',
                'contact_name' => 'Pengurus Purwokerto',
                'contact_phone' => '628975851952',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'ajibarang',
                'name' => 'Ajibarang',
                'address' => 'Jl. Raya Ajibarang No.— , Banyumas',
                'contact_name' => 'Pengurus Ajibarang',
                'contact_phone' => '628975851952',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'jakarta',
                'name' => 'Jakarta',
                'address' => 'Jl. — No.— , Jakarta',
                'contact_name' => 'Pengurus Jakarta',
                'contact_phone' => '628975851952',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pickup_locations');
    }
};
