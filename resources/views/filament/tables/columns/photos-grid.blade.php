@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $photos = $getState() ?? []; // array path relatif/URL
@endphp

<div x-data="{ open:false, src:null }"
     style="display:grid;grid-template-columns:repeat(3,60px);gap:6px;align-items:start;">
    @foreach($photos as $p)
        @php
            $url = Str::startsWith($p, ['http://','https://','/']) ? $p : Storage::disk('public')->url($p);
        @endphp
        <img src="{{ $url }}" alt="foto"
             @click.stop="src='{{ $url }}'; open=true"
             style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;cursor:zoom-in;">
    @endforeach

    <div x-show="open" x-transition @click="open=false"
         style="position:fixed;inset:0;background:rgba(0,0,0,.6);
                display:flex;align-items:center;justify-content:center;z-index:9999;">
        <img :src="src" alt="zoom"
             style="max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,.4);">
    </div>
</div>
