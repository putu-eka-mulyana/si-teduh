<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
  /**
   * Store a newly created push subscription.
   */
  public function store(Request $request)
  {
    $request->validate([
      'endpoint' => 'required|string',
      'keys.p256dh' => 'required|string',
      'keys.auth' => 'required|string',
    ]);

    $user = Auth::user();

    // Delete existing subscription for this endpoint
    PushSubscription::where('user_id', $user->id)
      ->where('endpoint', $request->endpoint)
      ->delete();

    // Create new subscription
    PushSubscription::create([
      'user_id' => $user->id,
      'endpoint' => $request->endpoint,
      'p256dh' => $request->keys['p256dh'],
      'auth' => $request->keys['auth'],
    ]);

    return response()->json(['message' => 'Push subscription saved successfully']);
  }

  /**
   * Remove the specified push subscription.
   */
  public function destroy(Request $request)
  {
    $request->validate([
      'endpoint' => 'required|string',
    ]);

    $user = Auth::user();

    PushSubscription::where('user_id', $user->id)
      ->where('endpoint', $request->endpoint)
      ->delete();

    return response()->json(['message' => 'Push subscription removed successfully']);
  }
}
