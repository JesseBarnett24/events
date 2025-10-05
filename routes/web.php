<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate;
use App\Models\Product;
use App\Models\User;

//Route::get('/test', function(){
//    $user = User::find(1);
//    $prods = $user->products;
//    dd($prods);
//});
//Route::get('/', function () {
//    return view('welcome');
//});

//Route::resource('product', ProductController::class);

//Route::resource('product/create', ProductController::class)->middleware(Authenticate::class);

Route::get('/', [ProductController::class, 'index']);
Route::get('/product', [ProductController::class, 'index']);


Route::middleware('auth')->group(function () {
    Route::get('/product/create', [ProductController::class, 'create']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::get('/product/{id}/edit', [ProductController::class, 'edit']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
});
Route::get('product/{id}', [ProductController::class, 'show']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
