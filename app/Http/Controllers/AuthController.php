<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'kode_user' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'kode_user' => $request->kode_user,
            'password' => $request->password
        ])) {

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'teacher') {
                return redirect()->route('teacher.dashboard');
            }

            if ($user->role === 'student') {
                return redirect()->route('student.dashboard');
            }
        }

        return back()->withErrors([
            'kode_user' => 'ID atau password salah.',
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
