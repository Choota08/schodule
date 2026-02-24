<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;

        $schedules = $teacher->schedules()
            ->with(['classRoom','subject','session'])
            ->get();

        return view('teacher.dashboard', compact('teacher','schedules'));
    }
}
