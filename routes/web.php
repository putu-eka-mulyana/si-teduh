<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', function () {
    return view('login');
});

Route::get('/edukasi-hiv', function () {
    return view('education-hiv');
})->name('edukasi-hiv');
Route::get('/beranda', function () {
    return view('welcome');
})->name('beranda');
