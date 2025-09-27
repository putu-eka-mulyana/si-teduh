<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\User;
use App\Notifications\WebPushScheduleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushTestController extends Controller
{
  /**
   * Test endpoint untuk mengirim push notification
   */
  public function sendTestNotification(Request $request)
  {
    try {
      $userId = $request->input('user_id', 1);
      $title = $request->input('title', 'Test Notification SI TEDUH');
      $body = $request->input('body', 'Ini adalah test notification untuk memastikan sistem berfungsi dengan baik.');
      $url = $request->input('url', '/user');

      $user = User::find($userId);
      if (!$user) {
        return response()->json([
          'success' => false,
          'message' => 'User tidak ditemukan'
        ], 404);
      }

      // Cek subscription user
      $subscriptions = $user->pushSubscriptions;
      if ($subscriptions->isEmpty()) {
        return response()->json([
          'success' => false,
          'message' => 'User belum subscribe ke push notification',
          'subscription_count' => 0
        ]);
      }

      $webPush = new WebPush([
        'VAPID' => [
          'subject' => env('VAPID_SUBJECT'),
          'publicKey' => env('VAPID_PUBLIC_KEY'),
          'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
      ]);

      $successCount = 0;
      $errorCount = 0;
      $errors = [];

      foreach ($subscriptions as $subscription) {
        try {
          $report = $webPush->sendOneNotification(
            Subscription::create([
              'endpoint' => $subscription->endpoint,
              'keys' => [
                'p256dh' => $subscription->p256dh,
                'auth' => $subscription->auth,
              ],
            ]),
            json_encode([
              'title' => $title,
              'body' => $body,
              'icon' => '/images/logo-puskesmas.png',
              'badge' => '/images/logo-puskesmas.png',
              'data' => [
                'url' => $url,
                'test' => true,
                'timestamp' => now()->toISOString()
              ]
            ])
          );

          if ($report->isSuccess()) {
            $successCount++;
          } else {
            $errorCount++;
            $errors[] = [
              'endpoint' => $subscription->endpoint,
              'error' => $report->getReason()
            ];
          }
        } catch (\Exception $e) {
          $errorCount++;
          $errors[] = [
            'endpoint' => $subscription->endpoint,
            'error' => $e->getMessage()
          ];
        }
      }

      return response()->json([
        'success' => true,
        'message' => 'Test notification dikirim',
        'data' => [
          'user_id' => $userId,
          'user_name' => $user->name ?? $user->email,
          'total_subscriptions' => $subscriptions->count(),
          'success_count' => $successCount,
          'error_count' => $errorCount,
          'errors' => $errors,
          'notification_data' => [
            'title' => $title,
            'body' => $body,
            'url' => $url
          ]
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error mengirim test notification: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Test endpoint untuk broadcast notification
   */
  public function sendBroadcastTest(Request $request)
  {
    try {
      $title = $request->input('title', 'Broadcast Test SI TEDUH');
      $body = $request->input('body', 'Ini adalah broadcast test notification untuk semua user.');

      // Ambil semua user yang memiliki subscription
      $usersWithSubscription = User::whereHas('pushSubscriptions')->get();

      if ($usersWithSubscription->isEmpty()) {
        return response()->json([
          'success' => false,
          'message' => 'Tidak ada user yang subscribe ke push notification'
        ]);
      }

      $testData = [
        'title' => $title,
        'body' => $body,
        'icon' => '/images/logo-puskesmas.png',
        'badge' => '/images/logo-puskesmas.png',
        'data' => [
          'url' => '/user',
          'broadcast' => true,
          'timestamp' => now()->toISOString()
        ]
      ];

      // Kirim broadcast notification
      Notification::send($usersWithSubscription, new class($testData) extends \Illuminate\Notifications\Notification {
        private $data;

        public function __construct($data)
        {
          $this->data = $data;
        }

        public function via($notifiable)
        {
          return ['broadcast'];
        }

        public function toBroadcast($notifiable)
        {
          return new \Illuminate\Notifications\Messages\BroadcastMessage($this->data);
        }
      });

      return response()->json([
        'success' => true,
        'message' => 'Broadcast test notification dikirim',
        'data' => [
          'total_users' => $usersWithSubscription->count(),
          'notification_data' => $testData
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error mengirim broadcast test: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get subscription statistics
   */
  public function getSubscriptionStats()
  {
    try {
      $totalSubscriptions = PushSubscription::count();
      $totalUsers = User::whereHas('pushSubscriptions')->count();
      $usersWithMultipleSubscriptions = User::whereHas('pushSubscriptions', function ($query) {
        $query->havingRaw('COUNT(*) > 1');
      })->count();

      $subscriptionsByUser = User::withCount('pushSubscriptions')
        ->having('push_subscriptions_count', '>', 0)
        ->get()
        ->map(function ($user) {
          return [
            'user_id' => $user->id,
            'user_name' => $user->name ?? $user->email,
            'subscription_count' => $user->push_subscriptions_count
          ];
        });

      return response()->json([
        'success' => true,
        'data' => [
          'total_subscriptions' => $totalSubscriptions,
          'total_users_with_subscriptions' => $totalUsers,
          'users_with_multiple_subscriptions' => $usersWithMultipleSubscriptions,
          'subscriptions_by_user' => $subscriptionsByUser
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error mendapatkan statistik: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Test VAPID configuration
   */
  public function testVapidConfig()
  {
    try {
      $vapidPublic = env('VAPID_PUBLIC_KEY');
      $vapidPrivate = env('VAPID_PRIVATE_KEY');
      $vapidSubject = env('VAPID_SUBJECT');

      $config = [
        'vapid_public_key' => $vapidPublic ? 'Set' : 'Not Set',
        'vapid_private_key' => $vapidPrivate ? 'Set' : 'Not Set',
        'vapid_subject' => $vapidSubject ?: 'Not Set',
        'queue_connection' => config('queue.default'),
        'broadcast_driver' => config('broadcasting.default')
      ];

      $isValid = !empty($vapidPublic) && !empty($vapidPrivate) && !empty($vapidSubject);

      return response()->json([
        'success' => $isValid,
        'message' => $isValid ? 'VAPID configuration valid' : 'VAPID configuration invalid',
        'config' => $config,
        'is_valid' => $isValid
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error checking VAPID config: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Clear all subscriptions (untuk testing)
   */
  public function clearAllSubscriptions()
  {
    try {
      $count = PushSubscription::count();
      PushSubscription::truncate();

      return response()->json([
        'success' => true,
        'message' => "Berhasil menghapus {$count} subscription(s)",
        'deleted_count' => $count
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error clearing subscriptions: ' . $e->getMessage()
      ], 500);
    }
  }
}
