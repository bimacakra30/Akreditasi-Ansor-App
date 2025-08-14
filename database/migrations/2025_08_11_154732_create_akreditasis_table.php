<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('akreditasis', function (Blueprint $table) {
            $table->id();
            // A. User penginput (bukan login)
            $table->string('uploader_name');
            $table->string('uploader_email');

            // B. Keansoran
            $table->string('kota_kab');
            $table->string('kecamatan');
            $table->string('desa_kel');

            // Single photos (path relatif dari /public/uploads)
            $table->string('foto_sk')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_kta')->nullable();
            $table->string('data_file')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('akreditasis');
    }
};
