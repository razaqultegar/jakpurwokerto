<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // Identitas & registrasi
            $table->string('registration_number', 50)->nullable();
            $table->string('card_number', 50)->nullable();
            $table->string('nik', 40)->nullable();

            // Data pribadi
            $table->string('name');
            $table->string('pob', 100)->nullable();          // tempat lahir
            $table->date('dob')->nullable();                 // tanggal lahir
            $table->string('gender', 20)->nullable();        // L / P (teks biasa)
            $table->string('blood_type', 10)->nullable();    // O / A / B / AB / O- dst (teks biasa)
            $table->string('shirt_size', 10)->nullable();    // S / M / L / XL dst (teks biasa)

            // Alamat — simpan sebagai teks biasa, wilayah sebagai FK
            $table->text('address_street')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->unsignedInteger('regency_id')->nullable();
            $table->unsignedInteger('province_id')->nullable();

            // Kontak
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();

            // Status & masa berlaku
            $table->string('status', 30)->default('Menunggu Antrian');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            // Metadata
            $table->timestamp('registered_at')->nullable();

            $table->timestamps();

            $table->index('registration_number');
            $table->index('card_number');
            $table->index('nik');
            $table->index('status');
            $table->index('district_id');
            $table->index('regency_id');
            $table->index('province_id');

            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('regency_id')->references('id')->on('regencies')->nullOnDelete();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
