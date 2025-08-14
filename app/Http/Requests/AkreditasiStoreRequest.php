<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AkreditasiStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
{
    return [
        'uploader_name'  => ['required','string','max:150'],
        'uploader_email' => ['required','email','max:150'],
        'kota_kab'       => ['required','string','max:120'],
        'kecamatan'      => ['required','string','max:120'],
        'desa_kel'       => ['required','string','max:120'],

        'data_file'      => ['nullable','file','mimetypes:application/pdf','max:25600'],

        'foto_sk'        => ['nullable','array'],
        'foto_sk.*'      => ['file','image','mimes:jpg,jpeg,png,webp','max:25600'],

        'foto_ktp'       => ['nullable','array'],
        'foto_ktp.*'     => ['file','image','mimes:jpg,jpeg,png,webp','max:25600'],

        'foto_kta'       => ['nullable','array'],
        'foto_kta.*'     => ['file','image','mimes:jpg,jpeg,png,webp','max:25600'],

        'dokumentasi_photos'   => ['nullable','array'],
        'dokumentasi_photos.*' => ['file','image','mimes:jpg,jpeg,png,webp','max:25600'],
    ];
}
}
