<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:student']);
    }

    /**
     * Get student dashboard with their classes and schedules
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return response()->json([
                'message' => 'Student data not found',
            ], 404);
        }

        $classIds = $student->classRooms()->pluck('class_rooms.id');

        $schedules = Schedule::whereIn('class_room_id', $classIds)
            ->orderBy('day')
            ->orderBy('session_id')
            ->get();

        return response()->json([
            'student' => $student,
            'classes' => $student->classRooms,
            'schedules' => $schedules,
        ]);
    }
}
