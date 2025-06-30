<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas básicas para autenticación
Route::get('/login', function () {
    return redirect('/api/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/api/register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
