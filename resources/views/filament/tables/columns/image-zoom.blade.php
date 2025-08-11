@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $path = $getState(); // path relatif dari disk 'public' atau URL absolut
    $src  = $path
        ? (Str::startsWith($path, ['http://','https://','/']) ? $path : Storage::disk('public')->url($path))
        : null;
@endphp

@if ($src)
<div x-data="{ open:false }" style="display:inline-block;">
    <img src="{{ $src }}" alt="foto"
         @click.stop="open = true"
         style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;cursor:zoom-in;">
    <div x-show="open" x-transition @click="open=false"
         style="position:fixed;inset:0;background:rgba(0,0,0,.6);
                display:flex;align-items:center;justify-content:center;z-index:9999;">
        <img src="{{ $src }}" alt="zoom"
             style="max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,.4);">
    </div>
</div>
@endif
