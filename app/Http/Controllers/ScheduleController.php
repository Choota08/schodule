<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\SubSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class ScheduleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Schedule::with([
            'classRoom',
            'teacher.user',
            'subject',
            'subSubject',
            'session'
        ]);

        if ($request->day) {
            $query->where('day', $request->day);
        }

        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->class_room_id) {
            $query->where('class_room_id', $request->class_room_id);
        }

        return response()->json(
            $query->orderBy('session_id')->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'sub_subject_id'=> 'nullable|exists:sub_subjects,id',
            'session_id'    => 'required|exists:sessions,id',
            'day'           => [
                'required',
                Rule::in([
                    'monday','tuesday','wednesday',
                    'thursday','friday','saturday','sunday'
                ])
            ],
        ]);

        $teacher = Teacher::findOrFail($validated['teacher_id']);

        // 🔒 Validasi guru sesuai subject yang dipilih
        if ($teacher->subject_id != $validated['subject_id']) {
            return response()->json([
                'message' => 'Teacher does not teach selected subject.'
            ], 422);
        }

        // 🔒 Validasi sub subject milik subject
        if (!empty($validated['sub_subject_id'])) {
            $validSub = SubSubject::where('id', $validated['sub_subject_id'])
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$validSub) {
                return response()->json([
                    'message' => 'Sub subject does not belong to selected subject.'
                ], 422);
            }
        }

        $data = [
            'class_room_id' => $validated['class_room_id'],
            'teacher_id'    => $teacher->id,
            'subject_id'    => $teacher->subject_id, // 🔥 final subject ikut guru
            'sub_subject_id'=> $validated['sub_subject_id'] ?? null,
            'session_id'    => $validated['session_id'],
            'day'           => $validated['day'],
        ];

        // 🔥 Conflict Check - Class
        if (Schedule::hasClassConflict(
            $data['class_room_id'],
            $data['day'],
            $data['session_id']
        )) {
            return response()->json([
                'message' => 'Class already has schedule in this session.'
            ], 422);
        }

        // 🔥 Conflict Check - Teacher
        if (Schedule::hasTeacherConflict(
            $data['teacher_id'],
            $data['day'],
            $data['session_id']
        )) {
            return response()->json([
                'message' => 'Teacher already has schedule in this session.'
            ], 422);
        }

        try {
            $schedule = Schedule::create($data);

            return response()->json(
                $schedule->load([
                    'classRoom',
                    'teacher.user',
                    'subject',
                    'subSubject',
                    'session'
                ]),
                201
            );

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Schedule conflict detected at database level.'
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'sub_subject_id'=> 'nullable|exists:sub_subjects,id',
            'session_id'    => 'required|exists:sessions,id',
            'day'           => [
                'required',
                Rule::in([
                    'monday','tuesday','wednesday',
                    'thursday','friday','saturday','sunday'
                ])
            ],
        ]);

        $teacher = Teacher::findOrFail($validated['teacher_id']);

        // 🔒 Validasi guru sesuai subject
        if ($teacher->subject_id != $validated['subject_id']) {
            return response()->json([
                'message' => 'Teacher does not teach selected subject.'
            ], 422);
        }

        // 🔒 Validasi sub subject milik subject
        if (!empty($validated['sub_subject_id'])) {
            $validSub = SubSubject::where('id', $validated['sub_subject_id'])
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$validSub) {
                return response()->json([
                    'message' => 'Sub subject does not belong to selected subject.'
                ], 422);
            }
        }

        $data = [
            'class_room_id' => $validated['class_room_id'],
            'teacher_id'    => $teacher->id,
            'subject_id'    => $teacher->subject_id,
            'sub_subject_id'=> $validated['sub_subject_id'] ?? null,
            'session_id'    => $validated['session_id'],
            'day'           => $validated['day'],
        ];

        // 🔥 Conflict Check - Class (ignore current id)
        if (Schedule::hasClassConflict(
            $data['class_room_id'],
            $data['day'],
            $data['session_id'],
            $schedule->id
        )) {
            return response()->json([
                'message' => 'Class already has schedule in this session.'
            ], 422);
        }

        // 🔥 Conflict Check - Teacher (ignore current id)
        if (Schedule::hasTeacherConflict(
            $data['teacher_id'],
            $data['day'],
            $data['session_id'],
            $schedule->id
        )) {
            return response()->json([
                'message' => 'Teacher already has schedule in this session.'
            ], 422);
        }

        try {
            $schedule->update($data);

            return response()->json(
                $schedule->load([
                    'classRoom',
                    'teacher.user',
                    'subject',
                    'subSubject',
                    'session'
                ])
            );

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Schedule conflict detected at database level.'
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully.'
        ]);
    }
}
