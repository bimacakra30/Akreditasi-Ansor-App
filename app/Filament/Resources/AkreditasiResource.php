<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AkreditasiResource\Pages;
use App\Models\Akreditasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

class AkreditasiResource extends Resource
{
    protected static ?string $model = Akreditasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Akreditasi';
    protected static ?string $pluralLabel = 'Akreditasi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('User Penginput')->schema([
                Forms\Components\TextInput::make('uploader_name')->label('Nama pengguna')->required(),
                Forms\Components\TextInput::make('uploader_email')->label('Email pengguna')->email()->required(),
            ])->columns(2),

            Forms\Components\Section::make('Keansoran')->schema([
                Forms\Components\TextInput::make('kota_kab')->label('Kota/Kabupaten')->required(),
                Forms\Components\TextInput::make('kecamatan')->required(),
                Forms\Components\TextInput::make('desa_kel')->label('Desa/Kelurahan')->required(),
            ])->columns(3),

            Forms\Components\Section::make('Dokumen Identitas')->schema([
                Forms\Components\FileUpload::make('foto_sk')
                    ->label('Foto SK')->image()->disk('public')->directory('akreditasi/sk')->openable()->downloadable(),
                Forms\Components\FileUpload::make('foto_ktp')
                    ->label('Foto KTP')->image()->disk('public')->directory('akreditasi/ktp')->openable()->downloadable(),
                Forms\Components\FileUpload::make('foto_kta')
                    ->label('Foto KTA')->image()->disk('public')->directory('akreditasi/kta')->openable()->downloadable(),
            ])->columns(3),

            Forms\Components\Section::make('Kebanseran (banyak foto)')->schema([
                Forms\Components\Repeater::make('kebanseranPhotos')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('akreditasi/kebanseran')
                            ->openable()
                            ->downloadable()
                            ->required(),
                    ])
                    ->addActionLabel('Tambah Foto')
                    ->grid(3),
            ]),

            Forms\Components\Section::make('Dokumentasi (banyak foto)')->schema([
                Forms\Components\Repeater::make('dokumentasiPhotos')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('akreditasi/dokumentasi')
                            ->openable()
                            ->downloadable()
                            ->required(),
                    ])
                    ->addActionLabel('Tambah Foto')
                    ->grid(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // penting: supaya klik gambar tidak membuka halaman Edit
            ->recordUrl(null)
            ->columns([
                TextColumn::make('no')->label('NO')
                    ->rowIndex()->alignCenter()->width('70px'),

                TextColumn::make('uploader_name')->label('NAMA')->searchable(),
                TextColumn::make('uploader_email')->label('EMAIL')->wrap()->searchable(),

                // KEANSORAN (computed)
                TextColumn::make('keansoran')->label('KEANSORAN')
                    ->html() // izinkan HTML
                    ->state(fn (Akreditasi $r) =>
                        nl2br(e("Kota/Kabupaten: {$r->kota_kab}\nKecamatan: {$r->kecamatan}\nDesa/kelurahan: {$r->desa_kel}"))
                    )
                    ->wrap(),

                // Single images dengan zoom
                ViewColumn::make('foto_sk')->label('FOTO SK')
                    ->view('filament.tables.columns.image-zoom'),
                ViewColumn::make('foto_ktp')->label('FOTO KTP')
                    ->view('filament.tables.columns.image-zoom'),
                ViewColumn::make('foto_kta')->label('FOTO KTA')
                    ->view('filament.tables.columns.image-zoom'),

                // Grid thumbnails + zoom
                ViewColumn::make('kebanseran')->label('LIST FOTO KEBANSERAN')
                    ->view('filament.tables.columns.photos-grid')
                    ->state(fn ($record) => $record->kebanseranPhotos->pluck('path')->all()),

                ViewColumn::make('dokumentasi')->label('LIST FOTO DOKUMENTASI')
                    ->view('filament.tables.columns.photos-grid')
                    ->state(fn ($record) => $record->dokumentasiPhotos->pluck('path')->all()),
            ])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAkreditasis::route('/'),
            'create' => Pages\CreateAkreditasi::route('/create'),
            'edit' => Pages\EditAkreditasi::route('/{record}/edit'),
        ];
    }
}
