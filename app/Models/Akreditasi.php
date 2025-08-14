<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Akreditasi extends Model
{
    protected $fillable = [
        'uploader_name',
        'uploader_email',
        'kota_kab',
        'kecamatan',
        'desa_kel',
        'foto_sk',
        'foto_ktp',
        'foto_kta',
        'data_file',
    ];

    public function dokumentasiPhotos(): HasMany
    {
        return $this->hasMany(DokumentasiPhoto::class);
    }
    public function skPhotos()
    {
        return $this->hasMany(\App\Models\SkPhoto::class);
    }
    public function ktpPhotos()
    {
        return $this->hasMany(\App\Models\KtpPhoto::class);
    }
    public function ktaPhotos()
    {
        return $this->hasMany(\App\Models\KtaPhoto::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $ak) {
            foreach (['foto_sk', 'foto_ktp', 'foto_kta', 'data_file'] as $col) {
                if (!empty($ak->$col)) {
                    Storage::disk('public')->delete($ak->$col);
                }
            }

            $ak->dokumentasiPhotos()->each(fn($p) => $p->delete());
        });

        static::updating(function (self $ak) {
            foreach (['foto_sk', 'foto_ktp', 'foto_kta', 'data_file'] as $col) {
                if ($ak->isDirty($col)) {
                    $old = $ak->getOriginal($col);
                    if ($old && $old !== $ak->$col) {
                        Storage::disk('public')->delete($old);
                    }
                }
            }
        });
    }
}
