<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Session;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with([
            'classRoom',
            'teacher.user',
            'subject',
            'session'
        ])->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        $teachers = Teacher::with('user')->get();
        $subjects = Subject::all();
        $sessions = Session::all();

        return view('admin.schedules.create', compact(
            'classes',
            'teachers',
            'subjects',
            'sessions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required',
            'teacher_id' => 'required',
            'subject_id' => 'required',
            'day' => 'required',
            'session_id' => 'required',
        ]);

        // Cek bentrok
        $conflict = Schedule::where('day', $request->day)
            ->where('session_id', $request->session_id)
            ->where(function ($query) use ($request) {
                $query->where('teacher_id', $request->teacher_id)
                      ->orWhere('class_room_id', $request->class_room_id);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Jadwal bentrok!');
        }

        Schedule::create($request->all());

        return redirect()->route('schedules.index')
            ->with('success', 'Jadwal berhasil dibuat.');
    }
}
