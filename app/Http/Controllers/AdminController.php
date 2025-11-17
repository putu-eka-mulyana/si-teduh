<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Logic to retrieve and display the list of admins
        $admins = Admin::with("user")->get(); // Fetch all admins from the database
        return view('admin.list-admin', compact('admins'));
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
    public function store(Request $request) {}

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
        $admin = Admin::with('user')->findOrFail($id);

        $request->validate([
            'fullname' => 'required',
            'phone_number' => [
                'required',
                Rule::unique('users', 'phone_number')->ignore(optional($admin->user)->id),
            ],
            'jobtitle' => 'required',
            'password' => 'required|min:6|confirmed',
            "password_confirmation" => 'required_with:password|same:password',
        ]);
        $admin->user->update([
            'phone_number' => $request->phone_number,
        ]);
        $admin->update([
            'fullname' => $request->fullname,
            'jobtitle' => $request->jobtitle,
        ]);
        if ($request->password) {
            $admin->user->update([
                'password' => Hash::make($request->password),
            ]);
        }
        return redirect()->route('admin.list')->with('success', 'Admin berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::findOrFail($id);
        User::where("owner_id", $admin->id);
        $admin->delete();

        return redirect()->route('admin.list')->with('success', 'Admin berhasil dihapus.');
    }
}
