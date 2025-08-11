<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kebanseran_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akreditasi_id')->constrained()->cascadeOnDelete();
            $table->string('path'); // relatif dari /public/uploads
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('kebanseran_photos');
    }
};

