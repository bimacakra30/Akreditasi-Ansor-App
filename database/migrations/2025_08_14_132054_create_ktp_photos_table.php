<?php

// database/migrations/2025_08_14_000002_create_ktp_photos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ktp_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akreditasi_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ktp_photos');
    }
};

