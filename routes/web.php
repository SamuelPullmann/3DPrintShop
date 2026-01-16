<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::get('/search', function () {
    return view('home');
})->name('search');

Route::get('/auth', function () {
    return view('auth');
})->name('auth.show');

// Profile routes - only for authenticated users
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');
Route::post('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout routes
Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Product API routes - must be before the detail page route
Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::post('reviews', [ProductController::class, 'storeReview'])->name('reviews.store');
    Route::delete('reviews/{id}', [ProductController::class, 'deleteReview'])->name('reviews.delete');
});

// Product image route (lazy loading)
Route::get('/products/{id}/image', [ProductController::class, 'image'])->name('product.image');

// Product details page - must be AFTER API routes to avoid conflicts
Route::get('/products/{id}', [ProductController::class, 'showPage'])->name('product.details');
