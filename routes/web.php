<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/auth', function () {
    return view('auth');
})->name('auth.show');

// (voliteľne si neskôr doplníš POST routy na login/register)
Route::post('/login', function () {
    // handle login
})->name('login.submit');

Route::post('/register', function () {
    // handle register
})->name('register.submit');
