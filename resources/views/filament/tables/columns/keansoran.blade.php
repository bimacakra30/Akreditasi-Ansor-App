@php($data = $getState() ?? [])
<div class="text-xs leading-tight space-y-2">
    <div>
        <div class="font-semibold italic text-gray-800">Kota/Kabupaten:</div>
        <div class="text-sm">{{ $data['kota_kab'] ?? '-' }}</div>
    </div>
    <div>
        <div class="font-semibold italic text-gray-800">Kecamatan:</div>
        <div class="text-sm">{{ $data['kecamatan'] ?? '-' }}</div>
    </div>
    <div>
        <div class="font-semibold italic text-gray-800">Desa/Kelurahan:</div>
        <div class="text-sm">{{ $data['desa_kel'] ?? '-' }}</div>
    </div>
</div>
