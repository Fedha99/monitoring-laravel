<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // FR-002: manajemen perangkat — operator melihat, admin mengelola (DevicePolicy)
    Route::get('devices', [DeviceController::class, 'index'])->name('devices.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('devices/create', [DeviceController::class, 'create'])->name('devices.create');
        Route::post('devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::get('devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
        Route::put('devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
        Route::delete('devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    });
});

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Hook fondasi M1 — manajemen perangkat (FR-002) menyusul di milestone berikutnya
        Route::get('/', fn () => view('admin.index'))->name('index');
    });

require __DIR__.'/auth.php';
