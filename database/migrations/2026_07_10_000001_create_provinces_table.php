<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
