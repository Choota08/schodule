<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display list of users
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Store new user (Admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_user' => 'required|unique:users,kode_user',
            'name'      => 'required',
            'role'      => 'required|in:admin,teacher,student',
        ]);

        $user = User::create([
            'kode_user' => $request->kode_user,
            'name'      => $request->name,
            'password'  => Hash::make('123456'), // default password
            'role'      => $request->role,
        ]);

        // Jika Student
        if ($request->role === 'student') {
            Student::create([
                'user_id'   => $user->id,
                'kode_user' => $request->kode_user,
                'class'     => $request->class ?? null,
                'year'      => $request->year ?? null,
            ]);
        }

        // Jika Teacher
        if ($request->role === 'teacher') {
            Teacher::create([
                'user_id'        => $user->id,
                'kode_user'      => $request->kode_user,
                'specialization' => $request->specialization ?? null,
            ]);
        }

        return back()->with('success', 'User berhasil dibuat');
    }

    /**
     * Update user basic info
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'User berhasil diperbarui');
    }

    /**
     * Reset password
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make('123456')
        ]);

        return back()->with('success', 'Password berhasil direset ke 123456');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Karena relasi pakai cascade,
        // student/teacher akan ikut terhapus otomatis
        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}
