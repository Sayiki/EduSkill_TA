<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelatihan;

class PelatihanController extends Controller
{
    public function index() {
        return response()->json(Pelatihan::all());
    }

    /**
     * POST /api/pelatihan
     * Buat pelatihan baru
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'nama_pelatihan'       => 'required|string|max:255',
            'keterangan_pelatihan' => 'nullable|string',
            'jumlah_kuota'         => 'required|integer|min:1',
            'jumlah_peserta'       => 'nullable|integer|min:0',
            'waktu_pengumpulan'    => 'nullable|date',
        ]);

        $pel = Pelatihan::create($payload);

        return response()->json([
            'message' => 'Pelatihan berhasil dibuat.',
            'data'    => $pel,
        ], 201);
    }

    /**
     * GET /api/pelatihan/{id}
     * Tampilkan pelatihan berdasarkan ID
     */
    public function show($id)
    {
        $pel = Pelatihan::findOrFail($id);

        return response()->json([
            'data' => $pel
        ]);
    }

    /**
     * PUT /api/pelatihan/{id}
     * Update pelatihan berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        $pel = Pelatihan::findOrFail($id);

        $payload = $request->validate([
            'nama_pelatihan'       => 'sometimes|required|string|max:255',
            'keterangan_pelatihan' => 'sometimes|nullable|string',
            'jumlah_kuota'         => 'sometimes|required|integer|min:1',
            'jumlah_peserta'       => 'sometimes|nullable|integer|min:0',
            'waktu_pengumpulan'    => 'sometimes|nullable|date',
        ]);

        $pel->update($payload);

        return response()->json([
            'message' => 'Pelatihan berhasil diperbarui.',
            'data'    => $pel,
        ]);
    }

    /**
     * DELETE /api/pelatihan/{id}
     * Hapus (atau non-aktifkan) pelatihan berdasarkan ID
     */
    public function destroy($id)
    {
        $pel = Pelatihan::findOrFail($id);
        $pel->delete();

        return response()->json([
            'message' => 'Pelatihan berhasil dihapus.',
        ]);
    }
}
