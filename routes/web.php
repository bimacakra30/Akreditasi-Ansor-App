<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return (Auth::check())
        ? redirect()->route('filament.admin.pages.dashboard')
        : redirect()->route('filament.admin.auth.login');
})->name('root');

Route::fallback(function () {
    return (Auth::check())
        ? redirect()->route('filament.admin.pages.dashboard')
        : redirect()->route('filament.admin.auth.login');
});
