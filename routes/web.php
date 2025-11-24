<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

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

Route::post('/login', function () {
    // handle login
})->name('login.submit');

Route::post('/register', function () {
    // handle register
})->name('register.submit');
