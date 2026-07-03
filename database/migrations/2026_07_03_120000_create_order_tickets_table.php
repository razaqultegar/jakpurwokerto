<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('code', 12)->unique();
            $table->unsignedInteger('unit_index')->default(1);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
        });

        DB::table('orders')->whereNotNull('checkin_code')->orderBy('id')->chunkById(100, function ($orders) {
            foreach ($orders as $order) {
                DB::table('order_tickets')->insert([
                    'order_id' => $order->id,
                    'code' => $order->checkin_code,
                    'unit_index' => 1,
                    'checked_in_at' => $order->checked_in_at,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['checkin_code', 'checked_in_at']);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('checkin_code', 12)->nullable()->unique()->after('status');
            $table->timestamp('checked_in_at')->nullable()->after('checkin_code');
        });

        DB::table('order_tickets')->where('unit_index', 1)->orderBy('id')->chunkById(100, function ($tickets) {
            foreach ($tickets as $ticket) {
                DB::table('orders')->where('id', $ticket->order_id)->update([
                    'checkin_code' => $ticket->code,
                    'checked_in_at' => $ticket->checked_in_at,
                ]);
            }
        });

        Schema::dropIfExists('order_tickets');
    }
};
