<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('customer_name', 120);
            $table->string('customer_email', 160);
            $table->string('customer_phone', 20);
            $table->enum('shipping_method', ['pickup', 'kirim']);
            $table->string('customer_address', 500)->nullable();
            $table->json('item');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('amount_due');
            $table->enum('payment_type', ['dp', 'full']);
            $table->string('payment_method_type', 20);
            $table->string('payment_method_key', 40)->nullable();
            $table->json('payment_data');
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
