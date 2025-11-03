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

    Log::info('=== PUSH NOTIFICATION DEBUG: Job Instance Created ===', [
      'step' => 'JOB_CONSTRUCT',
      'schedule_id' => $schedule->id,
      'job_class' => static::class,
      'timestamp' => now()->toDateTimeString(),
      'schedule_data' => [
        'id' => $schedule->id,
        'patient_id' => $schedule->patient_id,
        'datetime' => $schedule->datetime,
        'type' => $schedule->type,
        'status' => $schedule->status,
      ]
    ]);
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $jobId = $this->job ? (method_exists($this->job, 'getJobId') ? $this->job->getJobId() : 'unknown') : 'no-job-context';
    $attempts = $this->job ? (method_exists($this->job, 'attempts') ? $this->job->attempts() : 'unknown') : 'no-job-context';

    Log::info('=== PUSH NOTIFICATION DEBUG: Job Handle Started ===', [
      'step' => 'JOB_HANDLE_START',
      'schedule_id' => $this->schedule->id,
      'job_uuid' => $jobId,
      'attempts' => $attempts,
      'timestamp' => now()->toDateTimeString(),
      'memory_usage' => memory_get_usage(true),
      'schedule_full_data' => [
        'id' => $this->schedule->id,
        'patient_id' => $this->schedule->patient_id,
        'officer_id' => $this->schedule->officer_id,
        'datetime' => $this->schedule->datetime,
        'type' => $this->schedule->type,
        'status' => $this->schedule->status,
        'message' => $this->schedule->message,
        'created_at' => $this->schedule->created_at,
      ]
    ]);

    // Reload schedule dengan relationships
    $this->schedule->load(['patient', 'officer']);

    Log::info('=== PUSH NOTIFICATION DEBUG: Loading Schedule Relationships ===', [
      'step' => 'LOAD_RELATIONSHIPS',
      'schedule_id' => $this->schedule->id,
      'patient_loaded' => $this->schedule->relationLoaded('patient'),
      'officer_loaded' => $this->schedule->relationLoaded('officer'),
      'patient_data' => $this->schedule->patient ? [
        'id' => $this->schedule->patient->id,
        'fullname' => $this->schedule->patient->fullname,
        'phone_number' => $this->schedule->patient->phone_number,
      ] : null
    ]);

    // Ambil user yang terkait dengan patient
    Log::info('=== PUSH NOTIFICATION DEBUG: Fetching User from Patient ===', [
      'step' => 'FETCH_USER',
      'schedule_id' => $this->schedule->id,
      'patient_id' => $this->schedule->patient_id,
    ]);

    $user = $this->schedule->patient->user;

    if (!$user) {
      Log::warning('=== PUSH NOTIFICATION DEBUG: User Not Found ===', [
        'step' => 'USER_NOT_FOUND',
        'schedule_id' => $this->schedule->id,
        'patient_id' => $this->schedule->patient_id,
        'patient_data' => [
          'id' => $this->schedule->patient->id,
          'fullname' => $this->schedule->patient->fullname ?? null,
        ],
        'error' => 'User not found for patient',
        'job_terminated' => true
      ]);
      return;
    }

    Log::info('=== PUSH NOTIFICATION DEBUG: User Found ===', [
      'step' => 'USER_FOUND',
      'user_id' => $user->id,
      'user_role' => $user->role,
      'user_owner_id' => $user->owner_id,
      'schedule_id' => $this->schedule->id,
    ]);

    // Cek apakah user memiliki push subscriptions
    Log::info('=== PUSH NOTIFICATION DEBUG: Checking Push Subscriptions ===', [
      'step' => 'CHECK_SUBSCRIPTIONS',
      'user_id' => $user->id,
      'schedule_id' => $this->schedule->id,
    ]);

    $subscriptions = $user->pushSubscriptions;

    Log::info('=== PUSH NOTIFICATION DEBUG: Subscriptions Retrieved ===', [
      'step' => 'SUBSCRIPTIONS_RETRIEVED',
      'user_id' => $user->id,
      'subscription_count' => $subscriptions->count(),
      'subscriptions_detail' => $subscriptions->map(function ($sub) {
        return [
          'id' => $sub->id,
          'endpoint_short' => substr($sub->endpoint, 0, 50) . '...',
          'endpoint_length' => strlen($sub->endpoint),
          'has_p256dh' => !empty($sub->p256dh),
          'has_auth' => !empty($sub->auth),
          'created_at' => $sub->created_at,
        ];
      })->toArray()
    ]);

    if ($subscriptions->isEmpty()) {
      Log::warning('=== PUSH NOTIFICATION DEBUG: No Subscriptions Found ===', [
        'step' => 'NO_SUBSCRIPTIONS',
        'user_id' => $user->id,
        'schedule_id' => $this->schedule->id,
        'message' => 'User has no push subscriptions registered',
        'job_terminated' => true
      ]);
      return;
    }

    // Setup WebPush dengan VAPID keys
    Log::info('=== PUSH NOTIFICATION DEBUG: Setting Up WebPush Configuration ===', [
      'step' => 'SETUP_WEBPUSH',
      'vapid_config' => [
        'subject_set' => !empty(env('VAPID_SUBJECT')),
        'public_key_set' => !empty(env('VAPID_PUBLIC_KEY')),
        'private_key_set' => !empty(env('VAPID_PRIVATE_KEY')),
        'public_key_length' => strlen(env('VAPID_PUBLIC_KEY', '')),
        'subject' => env('VAPID_SUBJECT'),
        'public_key_preview' => substr(env('VAPID_PUBLIC_KEY', ''), 0, 20) . '...',
      ]
    ]);

    $webPush = new WebPush([
      'VAPID' => [
        'subject' => env('VAPID_SUBJECT'),
        'publicKey' => env('VAPID_PUBLIC_KEY'),
        'privateKey' => env('VAPID_PRIVATE_KEY'),
      ],
    ]);

    Log::info('=== PUSH NOTIFICATION DEBUG: WebPush Instance Created ===', [
      'step' => 'WEBPUSH_CREATED',
      'schedule_id' => $this->schedule->id,
      'webpush_configured' => true
    ]);

    // Format waktu jadwal
    Log::info('=== PUSH NOTIFICATION DEBUG: Formatting Schedule Time ===', [
      'step' => 'FORMAT_TIME',
      'schedule_datetime' => $this->schedule->datetime,
      'datetime_raw' => $this->schedule->datetime,
    ]);

    $scheduleTime = \Carbon\Carbon::parse($this->schedule->datetime)
      ->locale('id')
      ->translatedFormat('l, d F Y H:i');

    Log::info('=== PUSH NOTIFICATION DEBUG: Schedule Time Formatted ===', [
      'step' => 'TIME_FORMATTED',
      'formatted_time' => $scheduleTime,
      'schedule_type' => $this->schedule->type,
    ]);

    // Buat payload notification
    Log::info('=== PUSH NOTIFICATION DEBUG: Creating Notification Payload ===', [
      'step' => 'CREATE_PAYLOAD',
      'schedule_id' => $this->schedule->id,
      'patient_name' => $this->schedule->patient->fullname ?? 'Unknown',
    ]);

    $payloadData = [
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
    ];

    $payload = json_encode($payloadData);

    Log::info('=== PUSH NOTIFICATION DEBUG: Payload Created ===', [
      'step' => 'PAYLOAD_CREATED',
      'user_id' => $user->id,
      'schedule_id' => $this->schedule->id,
      'subscriptions_count' => $subscriptions->count(),
      'payload_data' => $payloadData,
      'payload_json' => $payload,
      'payload_length' => strlen($payload),
    ]);

    // Queue notifications untuk semua subscriptions user
    Log::info('=== PUSH NOTIFICATION DEBUG: Starting to Queue Notifications ===', [
      'step' => 'QUEUE_START',
      'total_subscriptions' => $subscriptions->count(),
      'schedule_id' => $this->schedule->id,
    ]);

    $queuedCount = 0;
    $queueErrors = [];

    foreach ($subscriptions as $index => $subscription) {
      Log::info("=== PUSH NOTIFICATION DEBUG: Queueing Subscription #{$index} ===", [
        'step' => "QUEUE_SUBSCRIPTION_{$index}",
        'subscription_id' => $subscription->id,
        'subscription_index' => $index,
        'endpoint_short' => substr($subscription->endpoint, 0, 50) . '...',
        'endpoint_length' => strlen($subscription->endpoint),
      ]);

      try {
        $webPushSubscription = WebPushSubscription::create([
          'endpoint' => $subscription->endpoint,
          'keys' => [
            'p256dh' => $subscription->p256dh,
            'auth' => $subscription->auth,
          ],
        ]);

        Log::info("=== PUSH NOTIFICATION DEBUG: WebPushSubscription Object Created #{$index} ===", [
          'step' => "WEBPUSH_SUB_OBJECT_{$index}",
          'subscription_id' => $subscription->id,
          'endpoint_valid' => !empty($webPushSubscription->getEndpoint()),
        ]);

        $webPush->queueNotification($webPushSubscription, $payload);
        $queuedCount++;

        Log::info("=== PUSH NOTIFICATION DEBUG: Subscription Queued Successfully #{$index} ===", [
          'step' => "QUEUE_SUCCESS_{$index}",
          'subscription_id' => $subscription->id,
          'queued_count' => $queuedCount,
        ]);
      } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
        $queueErrors[] = [
          'subscription_id' => $subscription->id,
          'index' => $index,
          'error' => $errorMsg,
        ];

        Log::error("=== PUSH NOTIFICATION DEBUG: Queue Error #{$index} ===", [
          'step' => "QUEUE_ERROR_{$index}",
          'subscription_id' => $subscription->id,
          'endpoint' => substr($subscription->endpoint, 0, 50) . '...',
          'error' => $errorMsg,
          'trace' => $e->getTraceAsString()
        ]);
      }
    }

    Log::info('=== PUSH NOTIFICATION DEBUG: Queueing Completed ===', [
      'step' => 'QUEUE_COMPLETE',
      'total_subscriptions' => $subscriptions->count(),
      'queued_count' => $queuedCount,
      'queue_errors_count' => count($queueErrors),
      'queue_errors' => $queueErrors,
      'schedule_id' => $this->schedule->id,
    ]);

    // Kirim semua queued notifications
    Log::info('=== PUSH NOTIFICATION DEBUG: Starting to Send Notifications (Flush) ===', [
      'step' => 'FLUSH_START',
      'queued_count' => $queuedCount,
      'schedule_id' => $this->schedule->id,
      'before_flush' => true,
    ]);

    $successCount = 0;
    $failedCount = 0;
    $removedCount = 0;
    $errors = [];
    $reportIndex = 0;

    $flushResults = $webPush->flush();

    Log::info('=== PUSH NOTIFICATION DEBUG: Flush Executed ===', [
      'step' => 'FLUSH_EXECUTED',
      'reports_count' => is_countable($flushResults) ? count($flushResults) : 'unknown',
      'schedule_id' => $this->schedule->id,
    ]);

    foreach ($flushResults as $report) {
      $reportIndex++;

      Log::info("=== PUSH NOTIFICATION DEBUG: Processing Report #{$reportIndex} ===", [
        'step' => "PROCESS_REPORT_{$reportIndex}",
        'report_index' => $reportIndex,
        'endpoint' => substr($report->getEndpoint(), 0, 50) . '...',
        'is_success' => $report->isSuccess(),
      ]);

      if ($report->isSuccess()) {
        $successCount++;
        Log::info("=== PUSH NOTIFICATION DEBUG: Notification Sent Successfully #{$reportIndex} ===", [
          'step' => "SEND_SUCCESS_{$reportIndex}",
          'report_index' => $reportIndex,
          'endpoint' => $report->getEndpoint(),
          'success_count' => $successCount,
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

        Log::error("=== PUSH NOTIFICATION DEBUG: Notification Failed #{$reportIndex} ===", [
          'step' => "SEND_FAILED_{$reportIndex}",
          'report_index' => $reportIndex,
          'endpoint' => $report->getEndpoint(),
          'status_code' => $statusCode,
          'reason' => $error,
          'failed_count' => $failedCount,
          'response_headers' => $report->getResponse() ? $report->getResponse()->getHeaders() : null,
        ]);

        // Jika endpoint expired atau gone (404, 410), hapus dari database
        if (in_array($statusCode, [404, 410])) {
          Log::warning("=== PUSH NOTIFICATION DEBUG: Expired Subscription Detected #{$reportIndex} ===", [
            'step' => "EXPIRED_SUBSCRIPTION_{$reportIndex}",
            'status_code' => $statusCode,
            'endpoint' => $report->getEndpoint(),
            'action' => 'Will remove from database',
          ]);

          $subscription = $subscriptions->firstWhere('endpoint', $report->getEndpoint());
          if ($subscription) {
            $subscription->delete();
            $removedCount++;
            Log::info("=== PUSH NOTIFICATION DEBUG: Expired Subscription Removed #{$reportIndex} ===", [
              'step' => "SUBSCRIPTION_REMOVED_{$reportIndex}",
              'subscription_id' => $subscription->id,
              'endpoint' => $report->getEndpoint(),
              'removed_count' => $removedCount,
            ]);
          } else {
            Log::warning("=== PUSH NOTIFICATION DEBUG: Subscription Not Found for Removal #{$reportIndex} ===", [
              'step' => "SUBSCRIPTION_NOT_FOUND_{$reportIndex}",
              'endpoint' => $report->getEndpoint(),
            ]);
          }
        }
      }
    }

    Log::info('=== PUSH NOTIFICATION DEBUG: All Reports Processed ===', [
      'step' => 'FLUSH_COMPLETE',
      'total_reports' => $reportIndex,
      'success_count' => $successCount,
      'failed_count' => $failedCount,
      'removed_count' => $removedCount,
      'schedule_id' => $this->schedule->id,
    ]);

    // Log hasil akhir
    Log::info('=== PUSH NOTIFICATION DEBUG: Job Completed Successfully ===', [
      'step' => 'JOB_COMPLETE',
      'user_id' => $user->id,
      'schedule_id' => $this->schedule->id,
      'summary' => [
        'total_subscriptions' => $subscriptions->count(),
        'queued' => $queuedCount,
        'success' => $successCount,
        'failed' => $failedCount,
        'removed' => $removedCount,
      ],
      'errors' => $errors,
      'job_duration' => 'Completed',
      'timestamp' => now()->toDateTimeString(),
      'memory_usage_final' => memory_get_usage(true),
      'memory_peak' => memory_get_peak_usage(true),
    ]);

    Log::info('=== PUSH NOTIFICATION DEBUG: END OF JOB ===', [
      'step' => 'JOB_END',
      'schedule_id' => $this->schedule->id,
      'final_status' => $successCount > 0 ? 'SUCCESS' : ($failedCount > 0 ? 'PARTIAL_FAILURE' : 'COMPLETE_FAILURE'),
    ]);
  }
}
