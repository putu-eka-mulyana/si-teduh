<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

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
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'session_time' => 'required',
            'officer_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);
        Schedule::create([
            'patient_id' => $request->input('patient_id'),
            'datetime' => $request->input('session_time'),
            'officer_id' => $request->input('officer_id'),
            'status' => 1,
            'type' => $request->input('type'),
            'message' => $request->input('message'),
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
