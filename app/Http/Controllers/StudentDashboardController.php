<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        $classes = $student->classes()->with('schedules.session')->get();

        return view('student.dashboard', compact('student','classes'));
    }
}
