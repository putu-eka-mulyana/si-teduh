<?php

namespace App\Jobs;

use App\Models\Schedule;
use App\Notifications\WebPushScheduleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

    if ($user) {
      // Kirim web push notification ke user
      $user->notify(new WebPushScheduleNotification($this->schedule));
    }
  }
}
