<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserController extends Controller
{
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

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'message' => 'Detail user',
            'data' => $user
        ]);
    }

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

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function importTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new UsersImport('teacher');

        Excel::import($import, $request->file('file'));

        return response()->json([
            'message' => 'Teachers imported successfully',
            'failures' => $import->failures()
        ]);
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new UsersImport('student');

        Excel::import($import, $request->file('file'));

        return response()->json([
            'message' => 'Students imported successfully',
            'failures' => $import->failures()
        ]);
    }
}
