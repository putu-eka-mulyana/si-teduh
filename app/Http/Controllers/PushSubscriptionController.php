<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;

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

  /**
   * Test sending a web push notification to the authenticated user's subscriptions.
   */
  public function test()
  {
    $user = Auth::user();

    if (!$user) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    $subscriptions = PushSubscription::where('user_id', $user->id)->get();

    if ($subscriptions->isEmpty()) {
      return response()->json([
        'message' => 'Tidak ada subscription untuk user ini. Coba refresh halaman agar melakukan subscribe terlebih dahulu.',
        'user_id' => $user->id,
        'subscription_count' => 0
      ], 400);
    }

    // Log untuk debugging
    Log::info('Testing web push for user', [
      'user_id' => $user->id,
      'subscriptions' => $subscriptions->toArray()
    ]);

    $webPush = new WebPush([
      'VAPID' => [
        'subject' => env('VAPID_SUBJECT'),
        'publicKey' => env('VAPID_PUBLIC_KEY'),
        'privateKey' => env('VAPID_PRIVATE_KEY'),
      ],
    ]);

    $payload = json_encode([
      'title' => 'Tes Notifikasi - SI TEDUH',
      'body' => 'Ini adalah notifikasi uji coba dari endpoint /test/webpush pada ' . now()->format('H:i:s'),
      'icon' => '/images/logo-puskesmas.png',
      'badge' => '/images/logo-puskesmas.png',
      'data' => [
        'url' => '/user',
        'notification_type' => 'test_notification',
        'timestamp' => now()->toISOString(),
      ],
    ]);

    Log::info('Web push payload', ['payload' => $payload]);

    $queued = 0;
    foreach ($subscriptions as $subscription) {
      $webPush->queueNotification(
        WebPushSubscription::create([
          'endpoint' => $subscription->endpoint,
          'keys' => [
            'p256dh' => $subscription->p256dh,
            'auth' => $subscription->auth,
          ],
        ]),
        $payload
      );
      $queued++;
    }

    $success = 0;
    $failed = 0;
    $removed = 0;
    $errors = [];

    foreach ($webPush->flush() as $report) {
      if ($report->isSuccess()) {
        $success++;
        Log::info('Web push success', ['endpoint' => $report->getEndpoint()]);
      } else {
        $failed++;
        $statusCode = $report->getResponse() ? $report->getResponse()->getStatusCode() : null;
        $error = $report->getReason();

        $errors[] = [
          'endpoint' => $report->getEndpoint(),
          'status_code' => $statusCode,
          'reason' => $error
        ];

        Log::error('Web push failed', [
          'endpoint' => $report->getEndpoint(),
          'status_code' => $statusCode,
          'reason' => $error
        ]);

        if (in_array($statusCode, [404, 410])) {
          // Endpoint expired or gone, remove from DB
          PushSubscription::where('endpoint', $report->getEndpoint())->delete();
          $removed++;
        }
      }
    }

    $result = [
      'message' => 'Test web push diproses',
      'queued' => $queued,
      'success' => $success,
      'failed' => $failed,
      'removed' => $removed,
      'errors' => $errors,
      'user_id' => $user->id,
      'subscription_count' => $subscriptions->count()
    ];

    Log::info('Web push test result', $result);

    return response()->json($result);
  }

  /**
   * Debug endpoint untuk melihat status subscription
   */
  public function debug()
  {
    $user = Auth::user();

    if (!$user) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    $subscriptions = PushSubscription::where('user_id', $user->id)->get();

    $subscriptionDetails = $subscriptions->map(function ($sub) {
      return [
        'id' => $sub->id,
        'endpoint' => $sub->endpoint,
        'created_at' => $sub->created_at,
        'updated_at' => $sub->updated_at,
        'endpoint_short' => substr($sub->endpoint, 0, 50) . '...'
      ];
    });

    return response()->json([
      'user_id' => $user->id,
      'user_role' => $user->role,
      'subscription_count' => $subscriptions->count(),
      'subscriptions' => $subscriptionDetails,
      'vapid_public_key' => env('VAPID_PUBLIC_KEY'),
      'vapid_subject' => env('VAPID_SUBJECT'),
      'notification_permission' => 'Check browser console for this info'
    ]);
  }
}
