<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubSubject;

class SubSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function index()
    {
        $subSubjects = SubSubject::with('subject')->latest()->get();

        return response()->json([
            'message' => 'List sub subjects',
            'data' => $subSubjects
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255'
        ]);

        $subSubject = SubSubject::create([
            'subject_id' => $request->subject_id,
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Sub subject created',
            'data' => $subSubject
        ], 201);
    }

    public function show($id)
    {
        $subSubject = SubSubject::with('subject')->findOrFail($id);

        return response()->json([
            'message' => 'Detail sub subject',
            'data' => $subSubject
        ]);
    }

    public function update(Request $request, $id)
    {
        $subSubject = SubSubject::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $subSubject->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Sub subject updated',
            'data' => $subSubject
        ]);
    }

    public function destroy($id)
    {
        $subSubject = SubSubject::findOrFail($id);
        $subSubject->delete();

        return response()->json([
            'message' => 'Sub subject deleted'
        ]);
    }
}
