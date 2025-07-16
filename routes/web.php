<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

use App\Livewire\UserManagement;
use App\Livewire\AreaManagement;
use App\Livewire\EscritorioManagement;
use App\Livewire\TicketManagement;
use App\Livewire\VideoManagement;
use App\Http\Controllers\VideoUploadController;
use App\Livewire\ClientTicket;
use App\Livewire\TicketQueue;
use App\Livewire\DeskSelection;
use App\Livewire\Test;
use App\Livewire\TvDisplay;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tickets', ClientTicket::class)->name('client.tickets');

Route::get('/tv', TvDisplay::class)->name('tv.display');

// Group all admin routes under a single middleware and prefix group
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/videos', VideoManagement::class)->name('videos.index');
    Route::post('/video/upload', [VideoUploadController::class, 'upload'])->name('video.upload');

    Route::get('/escritorios', EscritorioManagement::class)->name('admin.escritorios');
    Route::get('/areas', AreaManagement::class)->name('areas.index');
    Route::get('/users', UserManagement::class)->name('admin.users');
});

// Tickets management accessible by admin or empleado
Route::middleware(['auth', 'role:admin|empleado'])->group(function () {
    Route::get('/admin/tickets', TicketManagement::class)->name('tickets.index');
});

// Authenticated user routes (non-admin)
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');


    Route::get('/user/select-desk', DeskSelection::class)->name('user.selectDesk');
    Route::get('/user/tickets', TicketQueue::class)->name('user.tickets');
});

// Public test route
Route::get('/test', Test::class)->name('test.index');


require __DIR__.'/auth.php';