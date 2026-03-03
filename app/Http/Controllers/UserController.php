<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // =========================
    // GET ALL USERS
    // =========================
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);

        return response()->json([
            'message' => 'List semua user',
            'data' => $users
        ]);
    }

    // =========================
    // STORE USER
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'kode_user' => 'required|string|max:50|unique:users,kode_user',
            'name'      => 'required|string|max:255',
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,teacher,student'
        ]);

        $user = User::create([
            'kode_user' => $request->kode_user,
            'name'      => $request->name,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data'    => $user
        ], 201);
    }

    // =========================
    // SHOW USER
    // =========================
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'message' => 'Detail user',
            'data' => $user
        ]);
    }

    // =========================
    // UPDATE USER
    // =========================
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'kode_user' => 'required|string|max:50|unique:users,kode_user,' . $id,
            'name'      => 'required|string|max:255',
            'role'      => 'required|in:admin,teacher,student'
        ]);

        $user->update([
            'kode_user' => $request->kode_user,
            'name'      => $request->name,
            'role'      => $request->role,
        ]);

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data'    => $user
        ]);
    }

    // =========================
    // UPDATE PASSWORD
    // =========================
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|min:6'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diupdate'
        ]);
    }

    // =========================
    // DELETE USER
    // =========================
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
