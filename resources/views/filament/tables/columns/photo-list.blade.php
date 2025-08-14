{{-- Photo list compact (no badge, no zoom) --}}
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $raw    = $getState()['photos'] ?? $getState() ?? [];
    $photos = collect($raw);

    $urls = $photos->map(function ($p) {
        $path = is_string($p)
            ? $p
            : (is_object($p) && isset($p->path) ? $p->path
                : (is_array($p) && isset($p['path']) ? $p['path'] : null));
        if (! $path) return null;

        return Str::startsWith($path, ['http://','https://','/'])
            ? $path
            : Storage::disk('public')->url($path);
    })->filter()->values();

    $count = $urls->count();
    $thumbClass = 'w-[80px] h-[80px]'; // Ukuran diperbesar
@endphp

<div class="text-center">
    {{-- Grid thumbnail seragam --}}
    <div class="grid grid-cols-2 gap-1 mx-auto" style="max-width: 176px;">
        @forelse ($urls->take(4) as $u)
            <img
                src="{{ $u }}"
                alt="foto"
                class="{{ $thumbClass }} object-cover rounded border border-gray-200"
                loading="lazy"
            >
        @empty
            {{-- Placeholder kosong --}}
            <div class="{{ $thumbClass }} rounded border border-dashed border-gray-300 bg-gray-50"></div>
            <div class="{{ $thumbClass }} rounded border border-dashed border-gray-300 bg-gray-50"></div>
        @endforelse

        {{-- "+X" tile jika lebih dari 4 --}}
        @if ($count > 4)
            <div class="{{ $thumbClass }} rounded border border-gray-200 bg-gray-50 flex items-center justify-center">
                <span class="text-xs text-gray-700 font-medium">+{{ $count - 4 }}</span>
            </div>
        @endif
    </div>
</div>
