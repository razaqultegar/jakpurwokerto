<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Log pendaftaran: Baru / Perpanjang, tracking seluruh riwayat keanggotaan
        Schema::create('member_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();

            $table->string('registration_type', 20)->default('Baru'); // Baru / Perpanjang
            $table->string('sector', 50)->nullable();                 // Purwokerto / Rantau / Ajibarang / Jak School / Jak Kampus
            $table->string('registration_number', 50)->nullable();
            $table->string('card_number', 50)->nullable();

            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamp('registered_at')->nullable();

            $table->timestamps();

            $table->index('member_id');
            $table->index('registration_type');
            $table->index('registered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_registrations');
    }
};
