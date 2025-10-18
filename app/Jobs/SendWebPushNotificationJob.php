<?php

namespace App\Jobs;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription as WebPushSubscription;

class SendWebPushNotificationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $schedule;

  /**
   * Create a new job instance.
   */
  public function __construct(Schedule $schedule)
  {
    $this->schedule = $schedule;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    // Ambil user yang terkait dengan patient
    $user = $this->schedule->patient->user;

    if (!$user) {
      Log::warning('SendWebPushNotificationJob: User not found for schedule', [
        'schedule_id' => $this->schedule->id,
        'patient_id' => $this->schedule->patient_id
      ]);
      return;
    }

    // Cek apakah user memiliki push subscriptions
    $subscriptions = $user->pushSubscriptions;
    if ($subscriptions->isEmpty()) {
      Log::info('SendWebPushNotificationJob: User has no push subscriptions', [
        'user_id' => $user->id,
        'schedule_id' => $this->schedule->id
      ]);
      return;
    }

    // Setup WebPush dengan VAPID keys
    $webPush = new WebPush([
      'VAPID' => [
        'subject' => env('VAPID_SUBJECT'),
        'publicKey' => env('VAPID_PUBLIC_KEY'),
        'privateKey' => env('VAPID_PRIVATE_KEY'),
      ],
    ]);

    // Format waktu jadwal
    $scheduleTime = \Carbon\Carbon::parse($this->schedule->datetime)
      ->locale('id')
      ->translatedFormat('l, d F Y H:i');

    // Buat payload notification
    $payload = json_encode([
      'title' => 'Pengingat Jadwal - SI TEDUH',
      'body' => "Jadwal {$this->schedule->type} Anda akan berlangsung dalam 1 jam pada {$scheduleTime}",
      'icon' => '/images/logo-puskesmas.png',
      'badge' => '/images/logo-puskesmas.png',
      'data' => [
        'schedule_id' => $this->schedule->id,
        'type' => $this->schedule->type,
        'datetime' => $this->schedule->datetime,
        'message' => $this->schedule->message,
        'patient_name' => $this->schedule->patient->fullname,
        'notification_type' => 'schedule_reminder',
        'url' => '/user'
      ]
    ]);

    Log::info('SendWebPushNotificationJob: Sending push notification', [
      'user_id' => $user->id,
      'schedule_id' => $this->schedule->id,
      'subscriptions_count' => $subscriptions->count(),
      'payload' => $payload
    ]);

    // Queue notifications untuk semua subscriptions user
    $queuedCount = 0;
    foreach ($subscriptions as $subscription) {
      try {
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
        $queuedCount++;
      } catch (\Exception $e) {
        Log::error('SendWebPushNotificationJob: Error queueing notification', [
          'subscription_id' => $subscription->id,
          'endpoint' => $subscription->endpoint,
          'error' => $e->getMessage()
        ]);
      }
    }

    // Kirim semua queued notifications
    $successCount = 0;
    $failedCount = 0;
    $removedCount = 0;
    $errors = [];

    foreach ($webPush->flush() as $report) {
      if ($report->isSuccess()) {
        $successCount++;
        Log::info('SendWebPushNotificationJob: Push notification sent successfully', [
          'endpoint' => $report->getEndpoint()
        ]);
      } else {
        $failedCount++;
        $statusCode = $report->getResponse() ? $report->getResponse()->getStatusCode() : null;
        $error = $report->getReason();

        $errors[] = [
          'endpoint' => $report->getEndpoint(),
          'status_code' => $statusCode,
          'reason' => $error
        ];

        Log::error('SendWebPushNotificationJob: Push notification failed', [
          'endpoint' => $report->getEndpoint(),
          'status_code' => $statusCode,
          'reason' => $error
        ]);

        // Jika endpoint expired atau gone (404, 410), hapus dari database
        if (in_array($statusCode, [404, 410])) {
          $subscription = $subscriptions->firstWhere('endpoint', $report->getEndpoint());
          if ($subscription) {
            $subscription->delete();
            $removedCount++;
            Log::info('SendWebPushNotificationJob: Removed expired subscription', [
              'subscription_id' => $subscription->id,
              'endpoint' => $report->getEndpoint()
            ]);
          }
        }
      }
    }

    // Log hasil akhir
    Log::info('SendWebPushNotificationJob: Job completed', [
      'user_id' => $user->id,
      'schedule_id' => $this->schedule->id,
      'queued' => $queuedCount,
      'success' => $successCount,
      'failed' => $failedCount,
      'removed' => $removedCount,
      'errors' => $errors
    ]);
  }
}
