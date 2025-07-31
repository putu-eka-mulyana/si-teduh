<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'ADMIN') {
            return redirect()->route('admin.list-patient');
        } else {
            return redirect()->route('user.view');
        }
    }
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

Route::middleware('role:ADMIN')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.list-patient');
        })->name('admin.dashboard');
        Route::get('/data-pasien', [PatientController::class, 'index'])->name('admin.list-patient');
        Route::get('/data-pasien/tambah', [AuthController::class, 'showRegisterPatientForm'])->name('admin.add-patient');
        Route::post('/data-pasien/tambah', [AuthController::class, 'register'])->name('admin.store-patient');
        Route::delete('/data-pasien/{id}', [PatientController::class, 'destroy'])->name('admin.delete-patient');
        Route::get('/data-pasien/edit/{id}', [PatientController::class, 'edit'])->name('admin.edit-patient');
        Route::post('/data-pasien/edit/{id}', [AuthController::class, 'update'])->name('admin.update-patient');

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
        Route::put('/edit/{id}', [AdminController::class, 'update'])->name('admin.update');
    });
});



// buat route untuk handle user view
Route::middleware('role:USER')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.view');
    });
    Route::post('/schedule/confirm/{id}', [UserController::class, 'update'])->name('schedule.confirm');

    // Push subscription routes
    Route::post('/api/push-subscription', [PushSubscriptionController::class, 'store'])->name('push.subscription.store');
    Route::delete('/api/push-subscription', [PushSubscriptionController::class, 'destroy'])->name('push.subscription.destroy');
});
