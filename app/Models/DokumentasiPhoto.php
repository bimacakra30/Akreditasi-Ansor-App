<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DokumentasiPhoto extends Model
{
    protected $fillable = ['akreditasi_id','path'];

    protected static function booted(): void
    {
        static::deleting(function (self $photo) {
            if ($photo->path) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }
}


