<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\SubSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class ScheduleController extends Controller
{
    /**
     * Get all schedules with optional filtering by day, teacher, or class
     */
    public function index(Request $request)
    {
        try {
            $query = Schedule::with([
                'classRoom',
                'teacher.user',
                'subject',
                'subSubject',
                'session',
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
                $query->orderBy('day')->orderBy('session_id')->get()
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve schedules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new schedule with conflict validation
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'class_room_id' => 'required|exists:class_rooms,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:teachers,id',
                'sub_subject_id' => 'nullable|exists:sub_subjects,id',
                'session_id' => 'required|exists:sessions,id',
                'day' => [
                    'required',
                    Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                ],
            ]);

            $teacher = Teacher::findOrFail($validated['teacher_id']);

            if ($teacher->subject_id != $validated['subject_id']) {
                return response()->json([
                    'message' => 'Validation failed',
                    'error' => 'Teacher does not teach the selected subject.',
                ], 422);
            }

            if (!empty($validated['sub_subject_id'])) {
                $validSub = SubSubject::where('id', $validated['sub_subject_id'])
                    ->where('subject_id', $validated['subject_id'])
                    ->exists();

                if (!$validSub) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'error' => 'Sub-subject does not belong to the selected subject.',
                    ], 422);
                }
            }

            $data = [
                'class_room_id' => $validated['class_room_id'],
                'teacher_id' => $teacher->id,
                'subject_id' => $teacher->subject_id,
                'sub_subject_id' => $validated['sub_subject_id'] ?? null,
                'session_id' => $validated['session_id'],
                'day' => $validated['day'],
            ];

            if (Schedule::hasClassConflict($data['class_room_id'], $data['day'], $data['session_id'])) {
                return response()->json([
                    'message' => 'Conflict detected',
                    'error' => 'This classroom already has a schedule in this session.',
                ], 409);
            }

            if (Schedule::hasTeacherConflict($data['teacher_id'], $data['day'], $data['session_id'])) {
                return response()->json([
                    'message' => 'Conflict detected',
                    'error' => 'This teacher already has a schedule in this session.',
                ], 409);
            }

            $schedule = Schedule::create($data);

            return response()->json(
                [
                    'message' => 'Schedule created successfully',
                    'data' => $schedule->load([
                        'classRoom',
                        'teacher.user',
                        'subject',
                        'subSubject',
                        'session',
                    ]),
                ],
                201
            );
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred',
                'error' => 'Failed to create schedule. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing schedule with conflict validation
     */
    public function update(Request $request, Schedule $schedule)
    {
        try {
            $validated = $request->validate([
                'class_room_id' => 'required|exists:class_rooms,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:teachers,id',
                'sub_subject_id' => 'nullable|exists:sub_subjects,id',
                'session_id' => 'required|exists:sessions,id',
                'day' => [
                    'required',
                    Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                ],
            ]);

            $teacher = Teacher::findOrFail($validated['teacher_id']);

            if ($teacher->subject_id != $validated['subject_id']) {
                return response()->json([
                    'message' => 'Validation failed',
                    'error' => 'Teacher does not teach the selected subject.',
                ], 422);
            }

            if (!empty($validated['sub_subject_id'])) {
                $validSub = SubSubject::where('id', $validated['sub_subject_id'])
                    ->where('subject_id', $validated['subject_id'])
                    ->exists();

                if (!$validSub) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'error' => 'Sub-subject does not belong to the selected subject.',
                    ], 422);
                }
            }

            $data = [
                'class_room_id' => $validated['class_room_id'],
                'teacher_id' => $teacher->id,
                'subject_id' => $teacher->subject_id,
                'sub_subject_id' => $validated['sub_subject_id'] ?? null,
                'session_id' => $validated['session_id'],
                'day' => $validated['day'],
            ];

            if (Schedule::hasClassConflict($data['class_room_id'], $data['day'], $data['session_id'], $schedule->id)) {
                return response()->json([
                    'message' => 'Conflict detected',
                    'error' => 'This classroom already has a schedule in this session.',
                ], 409);
            }

            if (Schedule::hasTeacherConflict($data['teacher_id'], $data['day'], $data['session_id'], $schedule->id)) {
                return response()->json([
                    'message' => 'Conflict detected',
                    'error' => 'This teacher already has a schedule in this session.',
                ], 409);
            }

            $schedule->update($data);

            return response()->json([
                'message' => 'Schedule updated successfully',
                'data' => $schedule->load([
                    'classRoom',
                    'teacher.user',
                    'subject',
                    'subSubject',
                    'session',
                ]),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred',
                'error' => 'Failed to update schedule. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a schedule
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();

            return response()->json([
                'message' => 'Schedule deleted successfully',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot delete schedule',
                'error' => 'Failed to delete schedule. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get teachers who teach a specific subject
     */
    public function teachersBySubject($subjectId)
    {
        try {
            return response()->json([
                'data' => Teacher::with('user')
                    ->where('subject_id', $subjectId)
                    ->get(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve teachers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sub-subjects of a specific subject
     */
    public function subSubjectsBySubject($subjectId)
    {
        try {
            return response()->json([
                'data' => SubSubject::where('subject_id', $subjectId)->get(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve sub-subjects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get schedules for a specific class
     */
    public function byClass($classRoomId)
    {
        try {
            return response()->json([
                'data' => Schedule::with([
                    'classRoom',
                    'teacher.user',
                    'subject',
                    'subSubject',
                    'session',
                ])
                    ->where('class_room_id', $classRoomId)
                    ->orderBy('day')
                    ->orderBy('session_id')
                    ->get(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve schedules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get schedules for a specific teacher
     */
    public function byTeacher($teacherId)
    {
        try {
            return response()->json([
                'data' => Schedule::with([
                    'classRoom',
                    'teacher.user',
                    'subject',
                    'subSubject',
                    'session',
                ])
                    ->where('teacher_id', $teacherId)
                    ->orderBy('day')
                    ->orderBy('session_id')
                    ->get(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve schedules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated teacher's own schedule
     */
    public function mySchedule(Request $request)
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found',
            ], 404);
        }

        return response()->json(
            Schedule::with([
                'classRoom',
                'subject',
                'subSubject',
                'session',
            ])
                ->where('teacher_id', $teacher->id)
                ->orderBy('day')
                ->orderBy('session_id')
                ->get()
        );
    }

    /**
     * Get authenticated student's class schedule
     */
    public function classSchedule(Request $request)
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'message' => 'Student not found',
            ], 404);
        }

        $classIds = $student->classRooms()->pluck('class_rooms.id');

        return response()->json(
            Schedule::with([
                'teacher.user',
                'subject',
                'subSubject',
                'session',
                'classRoom',
            ])
                ->whereIn('class_room_id', $classIds)
                ->orderBy('day')
                ->orderBy('session_id')
                ->get()
        );
    }
}
