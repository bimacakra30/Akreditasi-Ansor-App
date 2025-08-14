<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AkreditasiStoreRequest;
use App\Http\Resources\AkreditasiResource;
use App\Models\Akreditasi;
use App\Models\DokumentasiPhoto;
use App\Models\SkPhoto;
use App\Models\KtpPhoto;
use App\Models\KtaPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AkreditasiController extends Controller
{
    public function index()
    {
        return Akreditasi::latest()
            ->withCount([
                'skPhotos',
                'ktpPhotos',
                'ktaPhotos',
                'dokumentasiPhotos',
            ])
            ->paginate(20);
    }

    public function show(Akreditasi $akreditasi)
    {
        $akreditasi->load([
            'skPhotos',
            'ktpPhotos',
            'ktaPhotos',
            'dokumentasiPhotos',
        ]);

        return new AkreditasiResource($akreditasi);
    }

    public function store(AkreditasiStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $ak = new Akreditasi($request->only([
                'uploader_name',
                'uploader_email',
                'kota_kab',
                'kecamatan',
                'desa_kel',
            ]));

            if ($request->hasFile('data_file')) {
                $kota = $this->sanitize($request->input('kota_kab', ''));
                $kec  = $this->sanitize($request->input('kecamatan', ''));
                $desa = $this->sanitize($request->input('desa_kel', ''));

                $base = collect([$kota, $kec, $desa])->filter()->implode('_');
                $filename = $base ? "DATA_{$base}.pdf" : 'DATA_PDF.pdf';

                $dir = 'akreditasi/data';
                $finalName = $this->uniqueFilename($dir, $filename, disk: 'public');

                $path = $request->file('data_file')->storeAs($dir, $finalName, 'public');
                $ak->data_file = $path;
            }

            $ak->save();

            foreach ($this->filesOf($request, 'foto_sk') as $file) {
                $path = $file->store('akreditasi/sk', 'public');
                SkPhoto::create([
                    'akreditasi_id' => $ak->id,
                    'path'          => $path,
                ]);
            }

            foreach ($this->filesOf($request, 'foto_ktp') as $file) {
                $path = $file->store('akreditasi/ktp', 'public');
                KtpPhoto::create([
                    'akreditasi_id' => $ak->id,
                    'path'          => $path,
                ]);
            }

            foreach ($this->filesOf($request, 'foto_kta') as $file) {
                $path = $file->store('akreditasi/kta', 'public');
                KtaPhoto::create([
                    'akreditasi_id' => $ak->id,
                    'path'          => $path,
                ]);
            }

            foreach ($this->filesOf($request, 'dokumentasi_photos') as $file) {
                $path = $file->store('akreditasi/dokumentasi', 'public');
                DokumentasiPhoto::create([
                    'akreditasi_id' => $ak->id,
                    'path'          => $path,
                ]);
            }

            $ak->load([
                'skPhotos',
                'ktpPhotos',
                'ktaPhotos',
                'dokumentasiPhotos',
            ]);

            return (new AkreditasiResource($ak))
                ->response()
                ->setStatusCode(201);
        });
    }

    private function filesOf($request, string $key): array
    {
        if (!$request->hasFile($key)) {
            return [];
        }

        $files = $request->file($key);

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        return is_array($files) ? array_filter($files) : [];
    }

    private function sanitize(string $s): string
    {
        $raw = trim(mb_strtoupper($s));
        $rep = preg_replace('/[^A-Z0-9]+/u', '_', $raw) ?? '';
        return trim($rep, '_');
    }
    private function uniqueFilename(string $dir, string $filename, string $disk = 'public'): string
    {

        $dotPos = strrpos($filename, '.');
        $name = $dotPos !== false ? substr($filename, 0, $dotPos) : $filename;
        $ext  = $dotPos !== false ? substr($filename, $dotPos + 1) : '';

        $candidate = $filename;
        $i = 1;
        while (Storage::disk($disk)->exists("$dir/$candidate")) {
            $suffix = "_{$i}";
            $candidate = $ext !== ''
                ? "{$name}{$suffix}.{$ext}"
                : "{$name}{$suffix}";
            $i++;
        }
        return $candidate;
    }
}
