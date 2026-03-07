<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function __construct()
    {
        // semua user login bisa melihat
        $this->middleware('auth:sanctum');

        // admin crud
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    //all
    public function index()
    {
        $subjects = Subject::with('subSubjects')->get();

        return response()->json([
            'data' => $subjects
        ]);
    }

    // detail subject
    public function show($id)
    {
        $subject = Subject::with('subSubjects')->findOrFail($id);

        return response()->json([
            'data' => $subject
        ]);
    }

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subject = Subject::create(
            $request->only('name')
        );

        return response()->json([
            'message' => 'Subject created successfully',
            'data' => $subject
        ], 201);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subject->update(
            $request->only('name')
        );

        return response()->json([
            'message' => 'Subject updated successfully',
            'data' => $subject
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }
}
