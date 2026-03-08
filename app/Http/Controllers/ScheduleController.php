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
            $query
                ->orderBy('day')
                ->orderBy('session_id')
                ->get()
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

        // Teacher harus sesuai subject
        if ($teacher->subject_id != $validated['subject_id']) {
            return response()->json([
                'message' => 'Teacher does not teach selected subject.'
            ], 422);
        }

        // Sub subject harus milik subject
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

        // Conflict Class
        if (Schedule::hasClassConflict(
            $data['class_room_id'],
            $data['day'],
            $data['session_id']
        )) {
            return response()->json([
                'message' => 'Class already has schedule in this session.'
            ], 422);
        }

        // Conflict Teacher
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

        if ($teacher->subject_id != $validated['subject_id']) {
            return response()->json([
                'message' => 'Teacher does not teach selected subject.'
            ], 422);
        }

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

        // Conflict Class
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

        // Conflict Teacher
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

    /*
    |--------------------------------------------------------------------------
    | TEACHERS BY SUBJECT
    |--------------------------------------------------------------------------
    */
    public function teachersBySubject($subjectId)
    {
        return response()->json(
            Teacher::with('user')
                ->where('subject_id', $subjectId)
                ->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SUB SUBJECTS BY SUBJECT
    |--------------------------------------------------------------------------
    */
    public function subSubjectsBySubject($subjectId)
    {
        return response()->json(
            SubSubject::where('subject_id', $subjectId)->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCHEDULE BY CLASS
    |--------------------------------------------------------------------------
    */
    public function byClass($classRoomId)
    {
        return response()->json(
            Schedule::with([
                'classRoom',
                'teacher.user',
                'subject',
                'subSubject',
                'session'
            ])
            ->where('class_room_id', $classRoomId)
            ->orderBy('day')
            ->orderBy('session_id')
            ->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCHEDULE BY TEACHER
    |--------------------------------------------------------------------------
    */
    public function byTeacher($teacherId)
    {
        return response()->json(
            Schedule::with([
                'classRoom',
                'teacher.user',
                'subject',
                'subSubject',
                'session'
            ])
            ->where('teacher_id', $teacherId)
            ->orderBy('day')
            ->orderBy('session_id')
            ->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TEACHER OWN SCHEDULE
    |--------------------------------------------------------------------------
    */
    public function mySchedule(Request $request)
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json(
            Schedule::with([
                'classRoom',
                'subject',
                'subSubject',
                'session'
            ])
            ->where('teacher_id', $teacher->id)
            ->orderBy('day')
            ->orderBy('session_id')
            ->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENT CLASS SCHEDULE
    |--------------------------------------------------------------------------
    */
    public function classSchedule(Request $request)
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }

        $classIds = $student->classRooms()->pluck('class_rooms.id');

        return response()->json(
            Schedule::with([
                'teacher.user',
                'subject',
                'subSubject',
                'session',
                'classRoom'
            ])
            ->whereIn('class_room_id', $classIds)
            ->orderBy('day')
            ->orderBy('session_id')
            ->get()
        );
    }
}
