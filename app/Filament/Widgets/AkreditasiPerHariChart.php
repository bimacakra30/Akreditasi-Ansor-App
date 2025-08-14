<?php

namespace App\Filament\Widgets;

use App\Models\Akreditasi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AkreditasiPerHariChart extends ChartWidget
{
    protected static ?string $heading = 'Akreditasi 7 Hari Terakhir';

    protected function getData(): array
    {
        $labels = [];
        $data   = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $labels[] = $d->format('d M');
            $data[]   = Akreditasi::whereDate('created_at', $d)->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Akreditasi',
                    'data'  => $data,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
