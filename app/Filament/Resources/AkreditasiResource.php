<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AkreditasiResource\Pages;
use App\Models\Akreditasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Get;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Enums\IconPosition;


class AkreditasiResource extends Resource
{
    protected static ?string $model = Akreditasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Akreditasi';
    protected static ?string $pluralLabel = 'Akreditasi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('User Penginput')
                ->schema([
                    Forms\Components\TextInput::make('uploader_name')
                        ->label('Nama pengguna')
                        ->required(),
                    Forms\Components\TextInput::make('uploader_email')
                        ->label('Email pengguna')
                        ->email()
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Keansoran')
                ->schema([
                    Forms\Components\TextInput::make('kota_kab')
                        ->label('Kota/Kabupaten')
                        ->required(),
                    Forms\Components\TextInput::make('kecamatan')
                        ->required(),
                    Forms\Components\TextInput::make('desa_kel')
                        ->label('Desa/Kelurahan')
                        ->required(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Dokumen Identitas')
                ->schema([
                    Forms\Components\Repeater::make('SkPhotos')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->label('Foto SK')
                                ->image()
                                ->disk('public')
                                ->directory('akreditasi/sk')
                                ->openable()
                                ->downloadable()
                                ->required(),
                        ])
                        ->addActionLabel('Tambah Foto SK')
                        ->grid(3),

                    Forms\Components\Repeater::make('ktpPhotos')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->label('Foto KTP')
                                ->image()
                                ->disk('public')
                                ->directory('akreditasi/ktp')
                                ->openable()
                                ->downloadable()
                                ->required(),
                        ])
                        ->addActionLabel('Tambah Foto KTP')
                        ->grid(3),

                    Forms\Components\Repeater::make('ktaPhotos')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->label('Foto KTA')
                                ->image()
                                ->disk('public')
                                ->directory('akreditasi/kta')
                                ->openable()
                                ->downloadable()
                                ->required(),
                        ])
                        ->addActionLabel('Tambah Foto KTA')
                        ->grid(3),
                ])
                ->columns(1),

            Forms\Components\Section::make('Data Ansor / Banser')
                ->schema([
                    Forms\Components\FileUpload::make('data_file')
                        ->label('Data Ansor / Banser via SiApps')
                        ->disk('public')
                        ->directory('akreditasi/data')
                        ->acceptedFileTypes(['application/pdf'])
                        ->preserveFilenames(false)
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                            $sanitize = fn(string $v) => strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', trim($v)));

                            $kotaKab   = $sanitize($get('kota_kab') ?? '');
                            $kecamatan = $sanitize($get('kecamatan') ?? '');
                            $desaKel   = $sanitize($get('desa_kel') ?? '');

                            $base = "DATA_{$kotaKab}_{$kecamatan}_{$desaKel}";
                            $ext  = $file->getClientOriginalExtension() ?: 'pdf';

                            return "{$base}.{$ext}";
                        })
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Forms\Components\Section::make('Dokumentasi (banyak foto)')
                ->schema([
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
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
            ])
            ->striped()
            ->columns([

                TextColumn::make('no')
                    ->label('NO')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('60px')
                    ->extraCellAttributes(['class' => 'text-center text-sm text-gray-600']),

                TextColumn::make('uploader_name')
                    ->label('NAMA')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('uploader_email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->label('EMAIL')
                    ->searchable()
                    ->limit(26)
                    ->tooltip(fn($state) => $state)
                    ->extraAttributes(['class' => 'text-gray-600 text-sm']),

                ViewColumn::make('keansoran')
                    ->label('KEANSORAN')
                    ->view('filament.tables.columns.keansoran')
                    ->state(fn(Akreditasi $record) => [
                        'kota_kab'  => $record->kota_kab,
                        'kecamatan' => $record->kecamatan,
                        'desa_kel'  => $record->desa_kel,
                    ])
                    ->extraCellAttributes(['class' => 'align-middle py-4']),

                ImageColumn::make('sk_preview')
                    ->label('FOTO SK')
                    ->getStateUsing(fn(Akreditasi $record) => optional($record->skPhotos()->first())->path)
                    ->disk('public')
                    ->height(100)->width(100)->square()
                    ->extraImgAttributes(['class' => 'object-cover'])
                    ->placeholder('/images/no-image.png')
                    ->tooltip(function (Akreditasi $record) {
                        $count = $record->skPhotos()->count();
                        return $count ? "Total {$count} foto SK" : null;
                    }),

                ImageColumn::make('ktp_preview')
                    ->label('FOTO KTP')
                    ->getStateUsing(fn(Akreditasi $record) => optional($record->ktpPhotos()->first())->path)
                    ->disk('public')
                    ->height(100)->width(100)->square()
                    ->extraImgAttributes(['class' => 'object-cover'])
                    ->placeholder('/images/no-image.png')
                    ->tooltip(function (Akreditasi $record) {
                        $count = $record->ktpPhotos()->count();
                        return $count ? "Total {$count} foto KTP" : null;
                    }),

                ImageColumn::make('kta_preview')
                    ->label('FOTO KTA')
                    ->getStateUsing(fn(Akreditasi $record) => optional($record->ktaPhotos()->first())->path)
                    ->disk('public')
                    ->height(100)->width(100)->square()
                    ->extraImgAttributes(['class' => 'object-cover'])
                    ->placeholder('/images/no-image.png')
                    ->tooltip(function (Akreditasi $record) {
                        $count = $record->ktaPhotos()->count();
                        return $count ? "Total {$count} foto KTA" : null;
                    }),

                TextColumn::make('data_file')
                    ->label("DATA ANGGOTA")
                    ->formatStateUsing(fn($state) => $state ? 'Lihat File' : 'Tidak Ada')
                    ->icon(fn($state) => $state ? 'heroicon-s-eye' : null)
                    ->iconPosition(IconPosition::Before)
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'gray')
                    ->url(
                        fn($record) => $record->data_file
                            ? Storage::disk('public')->url($record->data_file)
                            : null
                    )
                    ->openUrlInNewTab(),

                ImageColumn::make('dokumentasi_preview')
                    ->label('DOKUMENTASI')
                    ->getStateUsing(function (Akreditasi $record) {
                        $firstPhoto = $record->dokumentasiPhotos()->first();
                        return $firstPhoto ? $firstPhoto->path : null;
                    })
                    ->disk('public')
                    ->height(100)
                    ->width(100)
                    ->square()
                    ->extraImgAttributes(['class' => 'object-cover'])
                    ->placeholder('/images/no-image.png')
                    ->tooltip(function (Akreditasi $record) {
                        $count = $record->dokumentasiPhotos()->count();
                        return $count > 1 ? "Total {$count} foto dokumentasi." : null;
                    }),
            ])
            ->defaultSort('id', 'asc')
            ->actions([
                Tables\Actions\EditAction::make()->color('primary'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada data')
            ->emptyStateDescription('Klik "New akreditasi" untuk menambahkan data pertama.')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAkreditasis::route('/'),
            'create' => Pages\CreateAkreditasi::route('/create'),
            'edit' => Pages\EditAkreditasi::route('/{record}/edit'),
            'view' => Pages\ViewAkreditasi::route('/{record}'),
        ];
    }
}
