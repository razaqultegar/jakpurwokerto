<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Log perubahan status anggota (Menunggu Antrian, Dalam Proses, Telah Disetujui, Aktif, Tidak Aktif, Kadaluwarsa)
        Schema::create('member_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('reason', 150)->nullable();

            $table->timestamps();

            $table->index('member_id');
            $table->index('to_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_status_logs');
    }
};
