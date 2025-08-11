<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Akreditasi extends Model
{
    protected $fillable = [
        'uploader_name','uploader_email',
        'kota_kab','kecamatan','desa_kel',
        'foto_sk','foto_ktp','foto_kta',
    ];

    public function kebanseranPhotos(): HasMany {
        return $this->hasMany(KebanseranPhoto::class);
    }

    public function dokumentasiPhotos(): HasMany {
        return $this->hasMany(DokumentasiPhoto::class);
    }
}

