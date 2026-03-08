<?php

namespace App\Http\Controllers;

use App\Models\SubSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class SubSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all sub-subjects with their subjects
     */
    public function index()
    {
        try {
            $subSubjects = SubSubject::with('subject')->latest()->get();

            return response()->json([
                'message' => 'Sub-subjects list retrieved successfully',
                'data' => $subSubjects,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve sub-subjects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new sub-subject
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'name' => 'required|string|max:255',
            ]);

            $subSubject = SubSubject::create([
                'subject_id' => $request->subject_id,
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Sub-subject created successfully',
                'data' => $subSubject,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred',
                'error' => 'Failed to create sub-subject. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sub-subject details with subject
     */
    public function show($id)
    {
        try {
            $subSubject = SubSubject::with('subject')->findOrFail($id);

            return response()->json([
                'message' => 'Sub-subject details retrieved',
                'data' => $subSubject,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sub-subject not found',
                'error' => 'The requested sub-subject does not exist',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving sub-subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update sub-subject information
     */
    public function update(Request $request, $id)
    {
        try {
            $subSubject = SubSubject::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $subSubject->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Sub-subject updated successfully',
                'data' => $subSubject,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sub-subject not found',
                'error' => 'The sub-subject you are trying to update does not exist',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred',
                'error' => 'Failed to update sub-subject. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a sub-subject
     */
    public function destroy($id)
    {
        try {
            $subSubject = SubSubject::findOrFail($id);
            $subSubject->delete();

            return response()->json([
                'message' => 'Sub-subject deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sub-subject not found',
                'error' => 'The sub-subject you are trying to delete does not exist',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot delete sub-subject',
                'error' => 'This sub-subject has schedules. Please remove them first.',
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete sub-subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
