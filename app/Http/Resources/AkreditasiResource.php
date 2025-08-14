<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AkreditasiResource extends JsonResource
{
    public function toArray($request)
    {
        $this->resource->loadMissing([
            'skPhotos',
            'ktpPhotos',
            'ktaPhotos',
            'dokumentasiPhotos',
        ]);

        $url = fn (?string $path) => $path ? Storage::disk('public')->url($path) : null;

        $mapPhotos = fn ($collection) => $collection
            ->map(fn ($p) => [
                'id'   => $p->id,
                'path' => $url($p->path),
            ])
            ->values();

        $firstOrNull = fn ($collection) => $collection->first()
            ? $url($collection->first()->path)
            : null;

        return [
            'id'             => $this->id,
            'uploader_name'  => $this->uploader_name,
            'uploader_email' => $this->uploader_email,
            'kota_kab'       => $this->kota_kab,
            'kecamatan'      => $this->kecamatan,
            'desa_kel'       => $this->desa_kel,
            'sk'  => $mapPhotos($this->skPhotos),
            'ktp' => $mapPhotos($this->ktpPhotos),
            'kta' => $mapPhotos($this->ktaPhotos),
            'dokumentasi' => $mapPhotos($this->dokumentasiPhotos),
            'foto_sk'  => $firstOrNull($this->skPhotos),
            'foto_ktp' => $firstOrNull($this->ktpPhotos),
            'foto_kta' => $firstOrNull($this->ktaPhotos),
            'data_file'      => $url($this->data_file),
            'counts' => [
                'sk'          => $this->skPhotos->count(),
                'ktp'         => $this->ktpPhotos->count(),
                'kta'         => $this->ktaPhotos->count(),
                'dokumentasi' => $this->dokumentasiPhotos->count(),
            ],
            'created_at'     => $this->created_at,
        ];
    }
}
