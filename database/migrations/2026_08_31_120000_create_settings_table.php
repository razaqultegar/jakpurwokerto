<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('settings')->insert([
            [
                'key' => 'ticket_orders_open',
                'value' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'merchandise_orders_open',
                'value' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
