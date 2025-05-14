<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InformasiKontak;

class InformasiKontakController extends Controller
{
    public function index() {
        return response()->json(InformasiKontak::all());
    }

    /**
     * POST /api/informasi-kontak
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'alamat'  => 'required|string|max:1000',
            'email'   => 'required|email|max:255',
            'telepon' => 'required|string|max:50',
        ]);

        // tambahkan admin_id dari user yang sedang login
        $payload['admin_id'] = $request->user()->id;

        $kontak = InformasiKontak::create($payload);

        return response()->json([
            'message' => 'Kontak berhasil dibuat',
            'data'    => $kontak,
        ], 201);
    }

    /**
     * GET /api/informasi-kontak/{id}
     */
    public function show($id)
    {
        $kontak = InformasiKontak::findOrFail($id);

        return response()->json([
            'data' => $kontak
        ]);
    }

    /**
     * PUT /api/informasi-kontak/{id}
     */
    public function update(Request $request, $id)
    {
        $kontak = InformasiKontak::findOrFail($id);

        $payload = $request->validate([
            'alamat'  => 'required|string|max:1000',
            'email'   => 'required|email|max:255',
            'telepon' => 'required|string|max:50',
        ]);

        // jika ingin merekam siapa admin yang update
        $payload['admin_id'] = $request->user()->id;

        $kontak->update($payload);

        return response()->json([
            'message' => 'Kontak berhasil diperbarui',
            'data'    => $kontak,
        ]);
    }

    /**
     * DELETE /api/informasi-kontak/{id}
     */
    public function destroy($id)
    {
        $kontak = InformasiKontak::findOrFail($id);
        $kontak->delete();

        return response()->json([
            'message' => 'Kontak berhasil dihapus'
        ]);
    }
}
