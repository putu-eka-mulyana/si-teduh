<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');  // ambil query ?search=
        $perPage = $request->query('per_page', 10); // default 10 per halaman

        // Query builder awal
        $query = Patient::query();

        // Jika ada pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%");
            });
        }


        $total = $query->count();


        $patients = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.list-patient', [
            'patients' => $patients,
            'total' => $total,
            'search' => $search,
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ]);

        $patient = Patient::create($request->all());
        User::create([
            'owner_id' => $patient->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.list-patient')->with('success', 'Pasien berhasil ditambahkan.');
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
        $patient = Patient::findOrFail($id);
        return view('admin.add-patient', compact('patient', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $patient = Patient::findOrFail($id);
        $patient->update($request->all());
        if ($request->password != null) {
            $request->validate([
                'password' => 'required|min:6|confirmed',
                "password_confirmation" => 'required_with:password|same:password',
            ]);
            User::where('owner_id', $patient->id)->update([
                'phone_number' => $request->phone_number,
                'password' => bcrypt($request->password)
            ]);
        }
        return redirect()->route('admin.list-patient')->with('success', 'Pasien berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::findOrFail($id);
        User::where("owner_id", $patient->id);
        $patient->delete();

        return redirect()->route('admin.list-patient')->with('success', 'Pasien berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $search = $request->query('search');
        $patients = Patient::where('medical_record_number', 'like', "%$search%")->limit(5)->get();
        return response()->json($patients);
    }
}
