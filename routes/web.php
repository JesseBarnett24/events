<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrganiserDashboardController;

// Public routes for viewing events
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('events.show');

// Routes requiring authentication
Route::middleware('auth')->group(function () {
    // Profile management routes (Breeze scaffolding)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Event creation, editing, and deletion for organisers (controller handles role checks)
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

    // Organiser dashboard view
    Route::get('/organiser/{id}', [OrganiserDashboardController::class, 'dashboard'])
        ->middleware('auth')
        ->whereNumber('id')
        ->name('organiser.dashboard');
        
    // Attendee booking management
    Route::get('/bookings/mine', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{id}/cancel', [BookingController::class, 'destroy'])->name('bookings.destroy');
});

// Category routes for filtering or viewing specific category data
Route::get('/categories/{id}', [CategoryController::class, 'show'])
    ->whereNumber('id')
    ->name('categories.show');

// AJAX route for event filtering based on category or search
Route::get('/events/filter', [EventController::class, 'filter'])->name('events.filter');

// Redirect Breeze dashboard route to home page after login
Route::get('/dashboard', fn() => redirect()->route('home'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Static legal pages
Route::view('/privacy-policy', 'legal.privacy')->name('privacy.policy');
Route::view('/terms-of-use', 'legal.terms')->name('terms.use');

// Breeze authentication routes
require __DIR__.'/auth.php';
