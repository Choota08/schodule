<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:student']);
    }

    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return response()->json([
                'message' => 'Student data not found'
            ], 404);
        }

        //  Ambil semua kelas siswa
        $classIds = $student->classRooms()->pluck('class_rooms.id');

        //  Ambil semua jadwal dari kelas tersebut
        $schedules = Schedule::whereIn('class_room_id', $classIds)
            ->orderBy('day')
            ->orderBy('session_id')
            ->get();

        return response()->json([
            'student'   => $student,
            'classes'   => $student->classRooms,
            'schedules' => $schedules
        ]);
    }
}
