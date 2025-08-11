@php($photos = $getState() ?? [])
<div style="display:grid;grid-template-columns:repeat(3,60px);gap:6px;align-items:start;">
    @foreach($photos as $src)
        <img src="{{ $src }}" alt="foto" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #eee;">
    @endforeach
</div>
