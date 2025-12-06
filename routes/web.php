<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::get('/search', function () {
    return view('home');
})->name('search');

Route::get('/auth', function () {
    return view('auth');
})->name('auth.show');

// Cart page (simple placeholder)
Route::get('/cart', function () {
    return view('cart');
})->name('cart.show');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Product CRUD routes
Route::apiResource('products', ProductController::class);

// Product image route (lazy loading)
Route::get('/products/{id}/image', [ProductController::class, 'image'])->name('product.image');
