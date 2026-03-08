<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    /**
     * Get all subjects with their sub-subjects
     */
    public function index()
    {
        try {
            $subjects = Subject::with('subSubjects')->get();

            return response()->json([
                'data' => $subjects,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve subjects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subject details with sub-subjects
     */
    public function show($id)
    {
        try {
            $subject = Subject::with('subSubjects')->findOrFail($id);

            return response()->json([
                'data' => $subject,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Subject not found',
                'error' => 'The requested subject does not exist',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new subject
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $subject = Subject::create($request->only('name'));

            return response()->json([
                'message' => 'Subject created successfully',
                'data' => $subject,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred',
                'error' => 'Failed to create subject. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update subject information
     */
    public function update(Request $request, $id)
    {
        try {
            $subject = Subject::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $subject->update($request->only('name'));

            return response()->json([
                'message' => 'Subject updated successfully',
                'data' => $subject,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Subject not found',
                'error' => 'The subject you are trying to update does not exist',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error occurred',
                'error' => 'Failed to update subject. Please try again.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a subject
     */
    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();

            return response()->json([
                'message' => 'Subject deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Subject not found',
                'error' => 'The subject you are trying to delete does not exist',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot delete subject',
                'error' => 'This subject has sub-subjects or schedules. Please remove them first.',
            ], 409);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
