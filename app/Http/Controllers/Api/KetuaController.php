<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ketua;

class KetuaController extends Controller
{
    public function index()
    {
        $ketua = Ketua::with('user')->get();
        return response()->json($ketua);
    }

    /**
     * POST /api/ketua
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $ketua = Ketua::create($payload);

        return response()->json([
            'message' => 'Ketua berhasil dibuat.',
            'data'    => $ketua->load('user'),
        ], 201);
    }

    /**
     * GET /api/ketua/{id}
     */
    public function show($id)
    {
        $ketua = Ketua::with('user')->findOrFail($id);

        return response()->json(['data' => $ketua]);
    }

    /**
     * PUT /api/ketua/{id}
     */
    public function update(Request $request, $id)
    {
        $ketua = Ketua::findOrFail($id);

        $payload = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $ketua->update($payload);

        return response()->json([
            'message' => 'Ketua berhasil diperbarui.',
            'data'    => $ketua->load('user'),
        ]);
    }

    /**
     * DELETE /api/ketua/{id}
     */
    public function destroy($id)
    {
        $ketua = Ketua::findOrFail($id);
        $ketua->delete();

        return response()->json([
            'message' => 'Ketua berhasil dihapus.',
        ]);
    }
}
