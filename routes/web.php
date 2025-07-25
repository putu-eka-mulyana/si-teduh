<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScheduleController;
use App\Models\Patient;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('beranda');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/edukasi-hiv', function () {
    return view('education-hiv');
})->name('edukasi-hiv');
Route::get('/beranda', function () {
    return view('welcome');
})->name('beranda');


// admin routes

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/data-pasien', [PatientController::class, 'index'])->name('admin.list-patient');
    Route::get('/data-pasien/tambah', [AuthController::class, 'showRegisterPatientForm'])->name('admin.add-patient');
    Route::post('/data-pasien/tambah', [AuthController::class, 'register'])->name('admin.store-patient');
    Route::delete('/data-pasien/{id}', [PatientController::class, 'destroy'])->name('admin.delete-patient');
    Route::get('/data-pasien/edit/{id}', [PatientController::class, 'edit'])->name('admin.edit-patient');
    Route::post('/data-pasien/edit/{id}', [PatientController::class, 'update'])->name('admin.update-patient');

    // page scheduling
    Route::get('/data-jadwal', [ScheduleController::class, 'index'])->name('admin.list-schedule');
    Route::get('/data-jadwal/tambah', function () {
        return view('admin.add-schedule');
    })->name('admin.add-schedule');
    Route::post('/data-jadwal/tambah', [ScheduleController::class, 'store'])->name('admin.store-schedule');

    Route::get('/data-jadwal/edit/{id}', function ($id) {
        return view('admin.edit-schedule', ['id' => $id]);
    })->name('admin.edit-schedule');
    Route::put('/data-jadwal/edit/{id}', [ScheduleController::class, 'update'])->name('admin.update-schedule');

    Route::get('/list', [AdminController::class, 'index'])->name('admin.list');
    Route::post('/add', [AuthController::class, 'register'])->name('admin.store');
    Route::delete("/delete/{id}", [AdminController::class, 'destroy'])->name("admin.destroy");
});



// buat route untuk handle user view
Route::prefix('user')->group(function () {
    Route::get('/', function () {
        return view('user-view');
    })->name('user-view');
});
