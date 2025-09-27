<?php

use App\Http\Controllers\OfficerController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\WebPushTestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/officers', [OfficerController::class, 'index']);
Route::post('/officers', [OfficerController::class, 'store']);

Route::get('/patients', [PatientController::class, 'search']);

// Web Push Testing Routes
Route::prefix('webpush-test')->group(function () {
    Route::post('/send-notification', [WebPushTestController::class, 'sendTestNotification']);
    Route::post('/send-broadcast', [WebPushTestController::class, 'sendBroadcastTest']);
    Route::get('/stats', [WebPushTestController::class, 'getSubscriptionStats']);
    Route::get('/vapid-config', [WebPushTestController::class, 'testVapidConfig']);
    Route::delete('/clear-subscriptions', [WebPushTestController::class, 'clearAllSubscriptions']);
});

// Route::get('/send-test-push', function () {
//     $user = Auth::user(); // atau User::find(1)
//     $user->notify(new \App\Notifications\TestWebPushNotification());
//     return 'Notifikasi terkirim!';
// });
