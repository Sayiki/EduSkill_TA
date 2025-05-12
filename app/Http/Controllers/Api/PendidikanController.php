<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendidikan;

class PendidikanController extends Controller
{
    public function index() {
        return response()->json(Pendidikan::all());
    }

    /**
     * POST /api/pendidikan
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'nama_pendidikan' => 'required|string|max:255',
        ]);

        $pend = Pendidikan::create($payload);

        return response()->json([
            'message' => 'Pendidikan berhasil dibuat.',
            'data'    => $pend,
        ], 201);
    }

    /**
     * GET /api/pendidikan/{id}
     */
    public function show($id)
    {
        $pend = Pendidikan::findOrFail($id);

        return response()->json([
            'data' => $pend
        ]);
    }

    /**
     * PUT /api/pendidikan/{id}
     */
    public function update(Request $request, $id)
    {
        $pend = Pendidikan::findOrFail($id);

        $payload = $request->validate([
            'nama_pendidikan' => 'required|string|max:255',
        ]);

        $pend->update($payload);

        return response()->json([
            'message' => 'Pendidikan berhasil diperbarui.',
            'data'    => $pend,
        ]);
    }

    /**
     * DELETE /api/pendidikan/{id}
     */
    public function destroy($id)
    {
        $pend = Pendidikan::findOrFail($id);
        $pend->delete();

        return response()->json([
            'message' => 'Pendidikan berhasil dihapus.',
        ]);
    }
}
