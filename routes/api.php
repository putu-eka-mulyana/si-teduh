<?php

use App\Http\Controllers\OfficerController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/officers', [OfficerController::class, 'index']);
Route::post('/officers', [OfficerController::class, 'store']);

Route::get('/patients', [PatientController::class, 'search']);

// Route::get('/send-test-push', function () {
//     $user = Auth::user(); // atau User::find(1)
//     $user->notify(new \App\Notifications\TestWebPushNotification());
//     return 'Notifikasi terkirim!';
// });
