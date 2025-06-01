<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Peserta; // Untuk mengambil profil peserta dari user
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NotifikasiResource;
use Illuminate\Validation\Rule;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan notifikasi untuk peserta yang sedang login.
     * GET /api/notifikasi-saya
     */
    public function indexForCurrentUser(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) { // Asumsi relasi 'peserta' ada di model User
            return response()->json(['message' => 'Profil peserta tidak ditemukan untuk pengguna ini.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $perPage = $request->query('per_page', 10);
        $notifikasi = Notifikasi::where('id_peserta', $pesertaId)
                                ->latest() // Tampilkan yang terbaru dulu
                                ->paginate($perPage);

        return NotifikasiResource::collection($notifikasi);
    }

    /**
     * (Untuk Admin) Membuat notifikasi baru untuk peserta tertentu.
     * POST /api/notifikasi
     * Endpoint ini akan dilindungi oleh middleware peran admin.
     */
    public function storeForAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'id_peserta' => 'required|integer|exists:peserta,id',
            'pesan'      => 'required|string|max:1000',
            // Status defaultnya 'belum dibaca' saat dibuat
        ]);

        $validatedData['status'] = 'belum dibaca'; // Default status

        $notifikasi = Notifikasi::create($validatedData);

        return new NotifikasiResource($notifikasi);
    }

    /**
     * Menampilkan detail notifikasi spesifik milik peserta yang login.
     * GET /api/notifikasi-saya/{id_notifikasi}
     */
    public function showForCurrentUser(Request $request, $id_notifikasi)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $notifikasi = Notifikasi::where('id', $id_notifikasi)
                                ->where('id_peserta', $pesertaId)
                                ->firstOrFail();

        return new NotifikasiResource($notifikasi);
    }

    /**
     * (Untuk Peserta) Memperbarui status notifikasi (misalnya, menjadi 'dibaca').
     * PUT /api/notifikasi-saya/{id_notifikasi}
     */
    public function updateStatusForCurrentUser(Request $request, $id_notifikasi)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $notifikasi = Notifikasi::where('id', $id_notifikasi)
                                ->where('id_peserta', $pesertaId)
                                ->firstOrFail();

        $validatedData = $request->validate([
            'status' => ['required', Rule::in(['dibaca', 'belum dibaca'])],
        ]);

        $notifikasi->update($validatedData);

        return new NotifikasiResource($notifikasi);
    }

    /**
     * (Untuk Peserta) Menghapus notifikasi miliknya.
     * DELETE /api/notifikasi-saya/{id_notifikasi}
     */
    public function destroyForCurrentUser(Request $request, $id_notifikasi)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $notifikasi = Notifikasi::where('id', $id_notifikasi)
                                ->where('id_peserta', $pesertaId)
                                ->firstOrFail();
        
        $notifikasi->delete();

        return response()->json(['message' => 'Notifikasi berhasil dihapus.'], 200);
    }

}
