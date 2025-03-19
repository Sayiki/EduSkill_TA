<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberikanFeedback;
use Illuminate\Http\Request;

class MemberikanFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedbacks = MemberikanFeedback::all();
        return response()->json($feedbacks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_peserta' => 'required|uuid',
            'comment' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        $feedback = MemberikanFeedback::create($request->all());
        return response()->json($feedback, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $feedback = MemberikanFeedback::findOrFail($id);
        return response()->json($feedback);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'comment' => 'sometimes|string',
            'rating' => 'sometimes|integer|between:1,5',
        ]);

        $feedback = MemberikanFeedback::findOrFail($id);
        $feedback->update($request->all());
        return response()->json($feedback);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feedback = MemberikanFeedback::findOrFail($id);
        $feedback->delete();
        return response()->json(null, 204);
    }
}
