<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InformasiGaleri;

class InformasiGaleriController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $items = InformasiGaleri::paginate($perPage);

        return response()->json($items);
    }

    /**
     * POST /api/informasi-galeri
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'foto_galeri'   => 'nullable|url',
        ]);

        // tambahkan admin_id dari user yang sedang login
        $payload['admin_id'] = $request->user()->id;

        $item = InformasiGaleri::create($payload);

        return response()->json([
            'message' => 'Item galeri berhasil dibuat',
            'data'    => $item
        ], 201);
    }

    /**
     * GET /api/informasi-galeri/{id}
     */
    public function show($id)
    {
        $item = InformasiGaleri::findOrFail($id);

        return response()->json([
            'data' => $item
        ]);
    }

    /**
     * PUT /api/informasi-galeri/{id}
     */
    public function update(Request $request, $id)
    {
        $item = InformasiGaleri::findOrFail($id);

        $payload = $request->validate([
            'nama_kegiatan' => ['required','string','max:255'],
            'foto_galeri'   => ['nullable','url'],
        ]);

        // jika ingin merekam siapa admin yang update
        $payload['admin_id'] = $request->user()->id;

        $item->update($payload);

        return response()->json([
            'message' => 'Item galeri berhasil diperbarui',
            'data'    => $item
        ]);
    }

    /**
     * DELETE /api/informasi-galeri/{id}
     */
    public function destroy($id)
    {
        $item = InformasiGaleri::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Item galeri berhasil dihapus'
        ]);
    }
}
