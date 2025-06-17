<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\UserManagement;
use App\Livewire\AreaManagement;
use App\Livewire\EscritorioManagement;
use App\Livewire\TicketManagement;

Route::middleware(['auth', 'role:admin|empleado'])->group(function() {
    Route::get('/admin/tickets', TicketManagement::class)->name('tickets.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/escritorios', EscritorioManagement::class)->name('admin.escritorios');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/areas', AreaManagement::class)->name('areas.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', UserManagement::class)->name('admin.users');
});


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

require __DIR__.'/auth.php';
