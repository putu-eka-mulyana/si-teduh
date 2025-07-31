<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class WebPushScheduleNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public $schedule;

  /**
   * Create a new notification instance.
   */
  public function __construct(Schedule $schedule)
  {
    $this->schedule = $schedule;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return ['broadcast', 'database'];
  }

  /**
   * Get the broadcastable representation of the notification.
   */
  public function toBroadcast(object $notifiable): BroadcastMessage
  {
    $scheduleTime = \Carbon\Carbon::parse($this->schedule->datetime)->locale('id')->translatedFormat('l, d F Y H:i');

    return new BroadcastMessage([
      'id' => $this->id,
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
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return [
      'schedule_id' => $this->schedule->id,
      'type' => $this->schedule->type,
      'datetime' => $this->schedule->datetime,
      'message' => $this->schedule->message,
      'patient_name' => $this->schedule->patient->fullname,
      'notification_type' => 'schedule_reminder',
    ];
  }
}
