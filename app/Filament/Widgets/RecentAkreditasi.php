<?php

namespace App\Filament\Widgets;

use App\Models\Akreditasi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAkreditasi extends BaseWidget
{
    protected static ?string $heading = 'Akreditasi Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Akreditasi::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('uploader_name')
                    ->label('Nama')
                    ->weight('semibold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uploader_email')
                    ->label('Email')
                    ->wrap(),
                Tables\Columns\TextColumn::make('kota_kab')
                    ->label('Kota/Kab')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->paginated(false);
    }
}
