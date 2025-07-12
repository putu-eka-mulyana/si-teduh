<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Models\Patient;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('beranda');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::get('/edukasi-hiv', function () {
    return view('education-hiv');
})->name('edukasi-hiv');
Route::get('/beranda', function () {
    return view('welcome');
})->name('beranda');


// admin routes
Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/data-pasien', [PatientController::class, 'index'])->name('admin.list-patient');
Route::get('/admin/data-pasien/tambah', [AuthController::class, 'showRegisterPatientForm'])->name('admin.add-patient');
Route::post('/admin/data-pasien/tambah', [AuthController::class, 'register'])->name('admin.store-patient');
Route::delete('/admin/data-pasien/{id}', [PatientController::class, 'destroy'])->name('admin.delete-patient');
Route::get('/admin/data-pasien/edit/{id}', [PatientController::class, 'edit'])->name('admin.edit-patient');
Route::post('/admin/data-pasien/edit/{id}', [PatientController::class, 'update'])->name('admin.update-patient');

// page scheduling
Route::get('/admin/data-jadwal', function () {
    return view('admin.list-schedule');
})->name('admin.list-schedule');

Route::get('/admin/data-jadwal/tambah', function () {
    return view('admin.add-schedule');
})->name('admin.add-schedule');
Route::post('/admin/data-jadwal/tambah', function () {
    // Logic to handle adding a new schedule
    return redirect()->route('admin.list-schedule');
})->name('admin.store-schedule');

Route::get('/admin/data-jadwal/edit/{id}', function ($id) {
    return view('admin.edit-schedule', ['id' => $id]);
})->name('admin.edit-schedule');


// buat route untuk handle user view
Route::get('/user', function () {
    return view('user-view');
})->name('user-view');
