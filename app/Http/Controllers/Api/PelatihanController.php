<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelatihan;

class PelatihanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $pel = Pelatihan::paginate($perPage);

        return response()->json($pel);
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

        // tambahkan admin_id dari user yang sedang login
        $payload['admin_id'] = $request->user()->id;

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

        // jika ingin merekam siapa admin yang update
        $payload['admin_id'] = $request->user()->id;

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
