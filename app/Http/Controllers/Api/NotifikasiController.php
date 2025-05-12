<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index() {
        return response()->json(Notifikasi::all());
    }
    
    /**
     * POST /api/notifikasi
     * Buat notifikasi baru
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'pesan'  => 'required|string',
            'status' => 'required|string|in:unread,read',
        ]);

        $ntf = Notifikasi::create($payload);

        return response()->json([
            'message' => 'Notifikasi berhasil dibuat.',
            'data'    => $ntf,
        ], 201);
    }

    /**
     * GET /api/notifikasi/{id}
     * Tampilkan notifikasi berdasarkan ID
     */
    public function show($id)
    {
        $ntf = Notifikasi::findOrFail($id);
        return response()->json(['data' => $ntf]);
    }

    /**
     * PUT /api/notifikasi/{id}
     * Perbarui notifikasi berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        $ntf = Notifikasi::findOrFail($id);

        $payload = $request->validate([
            'pesan'  => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:unread,read',
        ]);

        $ntf->update($payload);

        return response()->json([
            'message' => 'Notifikasi berhasil diperbarui.',
            'data'    => $ntf,
        ]);
    }

    /**
     * DELETE /api/notifikasi/{id}
     * Hapus (atau non-aktifkan) notifikasi berdasarkan ID
     */
    public function destroy($id)
    {
        $ntf = Notifikasi::findOrFail($id);
        $ntf->delete();

        return response()->json([
            'message' => 'Notifikasi berhasil dihapus.',
        ]);
    }
}
