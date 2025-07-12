<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterPatientForm()
    {
        return view('admin.add-patient');
    }

    public function register(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:ADMIN,USER',
            "password_confirmation" => 'required_with:password|same:password',
        ]);
        $owner_id = null;
        if ($request->role === 'USER') {
            $patient = Patient::create([
                'phone_number' => $request->phone_number,
                'fullname' => $request->fullname,
                'medical_record_number' => $request->medical_record_number ?? '',
                'nik' => $request->nik ?? '',
                'bpjs_number' => $request->bpjs_number ?? '',
                'gender' => $request->gender ?? '',
                'birthday' => $request->birthday ?? '',
                'job_title' => $request->job_title ?? '',
                'address' => $request->address ?? '',
            ]);
            $owner_id = $patient->id;
        } elseif ($request->role === 'admin') {
            $admin = Admin::create([
                'jobtitle' => $request->jobtitle ?? '',
                'fullname' => $request->fullname ?? '',
            ]);
            $owner_id = $admin->id;
        }
        User::create([
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'owner_id' => $owner_id,
        ]);

        return redirect()->route('admin.list-patient')->with('success', 'Register berhasil. Silakan login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'phone_number' => 'Nomor telepon atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
