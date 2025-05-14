<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InformasiLembaga;

class InformasiLembagaController extends Controller
{
    public function index(){
        return response()->json(InformasiLembaga::all());
    }

    /**
     * POST /api/informasi-lembaga
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'visi' => 'required|string',
            'misi' => 'required|string',
        ]);

        // tambahkan admin_id dari user yang sedang login
        $payload['admin_id'] = $request->user()->id;

        $lembaga = InformasiLembaga::create($payload);

        return response()->json([
            'message' => 'Informasi lembaga berhasil dibuat.',
            'data'    => $lembaga,
        ], 201);
    }

    /**
     * GET /api/informasi-lembaga/{id}
     */
    public function show($id)
    {
        $lembaga = InformasiLembaga::findOrFail($id);

        return response()->json([
            'data' => $lembaga
        ]);
    }

    /**
     * PUT /api/informasi-lembaga/{id}
     */
    public function update(Request $request, $id)
    {
        $lembaga = InformasiLembaga::findOrFail($id);

        $payload = $request->validate([
            'visi' => 'required|string',
            'misi' => 'required|string',
        ]);

        // jika ingin merekam siapa admin yang update
        $payload['admin_id'] = $request->user()->id;

        $lembaga->update($payload);

        return response()->json([
            'message' => 'Informasi lembaga berhasil diperbarui.',
            'data'    => $lembaga,
        ]);
    }

    /**
     * DELETE /api/informasi-lembaga/{id}
     */
    public function destroy($id)
    {
        $lembaga = InformasiLembaga::findOrFail($id);
        $lembaga->delete();

        return response()->json([
            'message' => 'Informasi lembaga berhasil dihapus.',
        ]);
    }
}
