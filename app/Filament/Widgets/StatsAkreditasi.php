<?php

namespace App\Filament\Widgets;

use App\Models\Akreditasi;
use App\Models\DokumentasiPhoto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsAkreditasi extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAkreditasi = Akreditasi::count();

        $totalDokumentasi = class_exists(DokumentasiPhoto::class)
            ? DokumentasiPhoto::count()
            : 0;

        $today = Carbon::today();
        $todayNew = Akreditasi::whereDate('created_at', $today)->count();

        return [
            Stat::make('Total Akreditasi', number_format($totalAkreditasi))
                ->description($todayNew.' baru hari ini')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),

            Stat::make('Foto Dokumentasi', number_format($totalDokumentasi))
                ->description('Semua foto dokumentasi')
                ->descriptionIcon('heroicon-m-photo')
                ->color('warning'),
        ];
    }
}
