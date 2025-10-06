<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrganiserDashboardController;

// Public
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('events.show');

// Authenticated
Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Event CRUD (organiser check done inside controller)
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    Route::get('/events/{id}/edit', [EventController::class, 'edit'])
        ->whereNumber('id')
        ->name('events.edit');

    Route::put('/events/{id}', [EventController::class, 'update'])
        ->whereNumber('id')
        ->name('events.update');

    Route::delete('/events/{id}', [EventController::class, 'destroy'])
        ->whereNumber('id')
        ->name('events.destroy');


    Route::get('/organiser/{id}', [OrganiserDashboardController::class, 'dashboard'])
        ->middleware('auth')
        ->whereNumber('id')
        ->name('organiser.dashboard');
        
    // Attendee bookings
    Route::get('/bookings/mine', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{id}/cancel', [BookingController::class, 'destroy'])->name('bookings.destroy');
});

// Category filter
Route::get('/categories/{id}', [CategoryController::class, 'show'])
    ->whereNumber('id')
    ->name('categories.show');

// Breeze dashboard redirect
Route::get('/dashboard', fn() => redirect()->route('home'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/auth.php';
