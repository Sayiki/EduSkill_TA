<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Peserta; // Untuk mengambil profil peserta dari user dan semua peserta
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NotifikasiResource;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Untuk debugging jika perlu

class NotifikasiController extends Controller
{
    /**
     * Menampilkan notifikasi untuk peserta yang sedang login.
     * GET /api/notifikasi-saya
     */
    public function indexForCurrentUser(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) { 
            return response()->json(['message' => 'Profil peserta tidak ditemukan untuk pengguna ini.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $perPage = $request->query('per_page', 10);
        $notifikasi = Notifikasi::where('peserta_id', $pesertaId)
                                ->latest() 
                                ->paginate($perPage);

        return NotifikasiResource::collection($notifikasi);
    }

    /**
     * (Untuk Admin) Membuat notifikasi baru untuk peserta tertentu.
     * POST /api/admin/notifikasi
     */
    public function storeForAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'peserta_id' => 'required|integer|exists:peserta,id',
            'pesan'      => 'required|string|max:1000',
        ]);

        $validatedData['status'] = 'belum dibaca'; 

        $notifikasi = Notifikasi::create($validatedData);

        return new NotifikasiResource($notifikasi);
    }

    /**
     * (Untuk Admin) Mengirim notifikasi pengumuman ke semua peserta,
     * dengan opsi untuk mengecualikan beberapa peserta.
     * POST /api/admin/notifikasi/pengumuman
     */
    public function sendAnnouncementToAllPeserta(Request $request)
    {
        $validatedData = $request->validate([
            'pesan'                => 'required|string|max:1000',
            'excluded_peserta_ids' => 'nullable|array', // Array ID peserta yang dikecualikan
            'excluded_peserta_ids.*' => 'integer|exists:peserta,id', // Validasi setiap ID dalam array
        ]);

        $pesan = $validatedData['pesan'];
        $excludedIds = $request->input('excluded_peserta_ids', []); // Default array kosong jika tidak ada

        // Ambil semua ID peserta, kecuali yang dikecualikan
        $targetPesertaIds = Peserta::whereNotIn('id', $excludedIds)->pluck('id');

        if ($targetPesertaIds->isEmpty()) {
            return response()->json(['message' => 'Tidak ada peserta target untuk dikirimi notifikasi (mungkin semua dikecualikan atau tidak ada peserta).'], 400);
        }

        $notificationsData = [];
        $now = now(); // Waktu saat ini untuk created_at dan updated_at

        foreach ($targetPesertaIds as $pesertaId) {
            $notificationsData[] = [
                'peserta_id' => $pesertaId,
                'pesan' => $pesan,
                'status' => 'belum dibaca',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert semua notifikasi dalam satu query untuk efisiensi
        if (!empty($notificationsData)) {
            Notifikasi::insert($notificationsData);
        }

        return response()->json(['message' => 'Notifikasi pengumuman berhasil dikirim ke ' . count($targetPesertaIds) . ' peserta.'], 200);
    }


    /**
     * Menampilkan detail notifikasi spesifik milik peserta yang login.
     * GET /api/notifikasi-saya/{id_notifikasi}
     */
    public function showForCurrentUser(Request $request, $notifikasi_id)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $notifikasi = Notifikasi::where('id', $notifikasi_id)
                                ->where('peserta_id', $pesertaId)
                                ->firstOrFail();

        return new NotifikasiResource($notifikasi);
    }

    /**
     * (Untuk Peserta) Memperbarui status notifikasi (misalnya, menjadi 'dibaca').
     * PUT /api/notifikasi-saya/{id_notifikasi}
     */
    public function updateStatusForCurrentUser(Request $request, $notifikasi_id)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $notifikasi = Notifikasi::where('id', $notifikasi_id)
                                ->where('peserta_id', $pesertaId)
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
    public function destroyForCurrentUser(Request $request, $notifikasi_id)
    {
        $user = Auth::user();
        if (!$user || !$user->peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan.'], 404);
        }
        $pesertaId = $user->peserta->id;

        $notifikasi = Notifikasi::where('id', $notifikasi_id)
                                ->where('peserta_id', $pesertaId)
                                ->firstOrFail();
        
        $notifikasi->delete();

        return response()->json(['message' => 'Notifikasi berhasil dihapus.'], 200);
    }
}
