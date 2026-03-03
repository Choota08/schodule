<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:teacher']);
    }

    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher data not found'
            ], 404);
        }

        $today = strtolower(
            Carbon::now('Asia/Jakarta')->format('l')
        );

        $todaySchedules = Schedule::with([
                'classRoom:id,name',
                'subject:id,name',
                'subSubject:id,name',
                'session:id,name,start_time,end_time'
            ])
            ->where('teacher_id', $teacher->id)
            ->where('day', $today)
            ->orderBy('session_id')
            ->get();

        return response()->json([
            'teacher' => $teacher,
            'today' => $today,
            'today_schedules' => $todaySchedules
        ]);
    }
}
