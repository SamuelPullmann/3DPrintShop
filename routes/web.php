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

// Product API routes - must be before the detail page route
Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::post('reviews', [ProductController::class, 'storeReview'])->name('reviews.store');
});

// Product image route (lazy loading)
Route::get('/products/{id}/image', [ProductController::class, 'image'])->name('product.image');

// Product details page - must be AFTER API routes to avoid conflicts
Route::get('/products/{id}', [ProductController::class, 'showPage'])->name('product.details');
