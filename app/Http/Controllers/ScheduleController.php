<?php

namespace App\Http\Controllers;

use App\Jobs\SendWebPushNotificationJob;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');  // ambil query ?search=
        $perPage = $request->query('per_page', 10); // default 10 per halaman
        // Query builder awal
        $query = Schedule::with("patient", "officer");

        // Jika ada pencarian
        if ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('fullname', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%")
                    ->orWhere('medical_record_number', 'like', "%$search%");
            });
        }

        // Paginate
        $schedules = $query->orderBy('datetime', 'desc')->paginate($perPage);
        $total = $query->count();

        return view("admin.list-schedule", [
            "schedules" => $schedules,
            "search" => $search,
            "perPage" => $perPage,
            "total" => $total,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('=== PUSH NOTIFICATION DEBUG: Schedule Creation Started ===', [
            'step' => 'SCHEDULE_CREATE_START',
            'request_data' => $request->all(),
            'timestamp' => now()->toDateTimeString()
        ]);

        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'session_time' => 'required',
            'officer_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        Log::info('=== PUSH NOTIFICATION DEBUG: Validation Passed ===', [
            'step' => 'VALIDATION_PASSED',
            'validated_data' => [
                'patient_id' => $request->input('patient_id'),
                'session_time' => $request->input('session_time'),
                'officer_id' => $request->input('officer_id'),
                'type' => $request->input('type'),
                'message' => $request->input('message'),
            ]
        ]);

        $schedule = Schedule::create([
            'patient_id' => $request->input('patient_id'),
            'datetime' => $request->input('session_time'),
            'officer_id' => $request->input('officer_id'),
            'status' => 1,
            'type' => $request->input('type'),
            'message' => $request->input('message'),
        ]);

        Log::info('=== PUSH NOTIFICATION DEBUG: Schedule Created Successfully ===', [
            'step' => 'SCHEDULE_CREATED',
            'schedule_id' => $schedule->id,
            'schedule_data' => [
                'id' => $schedule->id,
                'patient_id' => $schedule->patient_id,
                'officer_id' => $schedule->officer_id,
                'datetime' => $schedule->datetime,
                'type' => $schedule->type,
                'status' => $schedule->status,
                'message' => $schedule->message,
                'created_at' => $schedule->created_at,
            ],
            'queue_connection' => config('queue.default'),
            'queue_name' => config('queue.connections.' . config('queue.default') . '.queue', 'default')
        ]);

        // Kirim web push notification ke user (1 jam sebelum jadwal)
        $scheduleDateTime = Carbon::parse($schedule->datetime);
        $notificationTime = $scheduleDateTime->copy()->subHour(); // 1 jam sebelum jadwal

        Log::info('=== PUSH NOTIFICATION DEBUG: Dispatching Job to Queue ===', [
            'step' => 'JOB_DISPATCH_START',
            'schedule_id' => $schedule->id,
            'job_class' => 'SendWebPushNotificationJob',
            'schedule_datetime' => $schedule->datetime,
            'notification_scheduled_for' => $notificationTime->toDateTimeString(),
            'current_time' => now()->toDateTimeString(),
            'delay_until' => $notificationTime->toDateTimeString(),
            'before_dispatch' => true
        ]);

        try {
            // Jika waktu notifikasi sudah lewat atau kurang dari 1 menit lagi, kirim langsung
            if ($notificationTime->isPast() || $notificationTime->diffInMinutes(now()) < 1) {
                Log::info('=== PUSH NOTIFICATION DEBUG: Notification time passed or too close, sending immediately ===', [
                    'step' => 'IMMEDIATE_DISPATCH',
                    'schedule_id' => $schedule->id,
                    'notification_time' => $notificationTime->toDateTimeString(),
                    'current_time' => now()->toDateTimeString(),
                ]);
                SendWebPushNotificationJob::dispatch($schedule);
            } else {
                // Jadwalkan job untuk 1 jam sebelum jadwal
                SendWebPushNotificationJob::dispatch($schedule)
                    ->delay($notificationTime);
            }

            Log::info('=== PUSH NOTIFICATION DEBUG: Job Dispatched Successfully ===', [
                'step' => 'JOB_DISPATCHED',
                'schedule_id' => $schedule->id,
                'job_class' => 'SendWebPushNotificationJob',
                'queue_connection' => config('queue.default'),
                'queue_name' => config('queue.connections.' . config('queue.default') . '.queue', 'default'),
                'notification_scheduled_for' => $notificationTime->toDateTimeString(),
                'timestamp' => now()->toDateTimeString(),
                'note' => 'Job ID will be available in job handle() method'
            ]);
        } catch (\Exception $e) {
            Log::error('=== PUSH NOTIFICATION DEBUG: Job Dispatch Failed ===', [
                'step' => 'JOB_DISPATCH_ERROR',
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        Log::info('=== PUSH NOTIFICATION DEBUG: Schedule Creation Completed ===', [
            'step' => 'SCHEDULE_CREATE_COMPLETE',
            'schedule_id' => $schedule->id,
            'next_step' => 'Job will be processed by queue worker'
        ]);

        return redirect()->route("admin.list-schedule");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::find($id);
        $schedule->status = 3;
        $schedule->save();
        return redirect()->route('admin.list-schedule');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
