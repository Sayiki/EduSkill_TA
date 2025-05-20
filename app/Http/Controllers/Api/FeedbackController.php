<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $fb = Feedback::with('peserta')
                    ->paginate($perPage);

        return response()->json($fb);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_peserta' => 'required|integer|exists:peserta,id',
            'comment'    => 'required|string',
            'rating'     => 'required|integer|min:1|max:5',
        ]);

        $fb = Feedback::create($data);

        return response()->json(
            $fb->load('peserta'),
            201
        );
    }

    /**
     * GET /api/feedback/{id}
     */
    public function show($id)
    {
        $fb = Feedback::with('peserta')->findOrFail($id);
        return response()->json($fb, 200);
    }

    /**
     * PUT /api/feedback/{id}
     */
    public function update(Request $request, $id)
    {
        $fb = Feedback::findOrFail($id);

        $data = $request->validate([
            'id_peserta' => 'sometimes|required|integer|exists:peserta,id',
            'comment'    => 'sometimes|required|string',
            'rating'     => 'sometimes|required|integer|min:1|max:5',
        ]);

        $fb->update($data);

        return response()->json(
            $fb->load('peserta'),
            200
        );
    }

    /**
     * DELETE /api/feedback/{id}
     */
    public function destroy($id)
    {
        $fb = Feedback::findOrFail($id);
        $fb->delete(); 

        return response()->json(null, 204);
    }
}
