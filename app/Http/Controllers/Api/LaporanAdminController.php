<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanAdmin;

class LaporanAdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $laporan = LaporanAdmin::paginate($perPage);

        return response()->json($laporan);
    }

    /**
     * POST /api/laporan-admin
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'jumlah_peserta'         => 'required|integer|min:0',
            'jumlah_lulusan_bekerja' => 'required|integer|min:0',
            'jumlah_pendaftar'       => 'required|integer|min:0',
            'pelatihan_dibuka'       => 'required|string|max:100',
            'pelatihan_berjalan'     => 'required|string|max:100',
        ]);

        // tambahkan admin_id dari user yang sedang login
        $payload['admin_id'] = $request->user()->id;

        $laporan = LaporanAdmin::create($payload);

        return response()->json([
            'message' => 'Laporan admin berhasil dibuat.',
            'data'    => $laporan,
        ], 201);
    }

    /**
     * GET /api/laporan-admin/{id}
     */
    public function show($id)
    {
        $laporan = LaporanAdmin::findOrFail($id);

        return response()->json([
            'data' => $laporan
        ]);
    }

    /**
     * PUT /api/laporan-admin/{id}
     */
    public function update(Request $request, $id)
    {
        $laporan = LaporanAdmin::findOrFail($id);

        $payload = $request->validate([
            'jumlah_peserta'         => 'sometimes|required|integer|min:0',
            'jumlah_lulusan_bekerja' => 'sometimes|required|integer|min:0',
            'jumlah_pendaftar'       => 'sometimes|required|integer|min:0',
            'pelatihan_dibuka'       => 'sometimes|required|string|max:100',
            'pelatihan_berjalan'     => 'sometimes|required|string|max:100',
        ]);

        // jika ingin merekam siapa admin yang update
        $payload['admin_id'] = $request->user()->id;

        $laporan->update($payload);

        return response()->json([
            'message' => 'Laporan admin berhasil diperbarui.',
            'data'    => $laporan,
        ]);
    }

    /**
     * DELETE /api/laporan-admin/{id}
     */
    public function destroy($id)
    {
        $laporan = LaporanAdmin::findOrFail($id);
        $laporan->delete();

        return response()->json([
            'message' => 'Laporan admin berhasil dihapus.',
        ]);
    }
}
