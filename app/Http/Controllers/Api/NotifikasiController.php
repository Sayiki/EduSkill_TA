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
            'judul'      => 'required|string|max:1000',
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
            'judul'      => 'required|string|max:1000',
            'pesan'                => 'required|string|max:1000',
            'excluded_peserta_ids' => 'nullable|array', // Array ID peserta yang dikecualikan
            'excluded_peserta_ids.*' => 'integer|exists:peserta,id', // Validasi setiap ID dalam array
        ]);

        $judul = $validatedData['judul'];
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
                'judul'      => $judul,
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
                            ->first();

        if (!$notifikasi) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan atau bukan milik Anda.'], 404);
        }

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
                                ->first();
        
        if (!$notifikasi) {
            return response()->json(['message' => 'Gagal memperbarui. Notifikasi tidak ditemukan atau bukan milik Anda.'], 404);
        }

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
                                ->first();
        
        if (!$notifikasi) {
            return response()->json(['message' => 'Gagal menghapus. Notifikasi tidak ditemukan atau bukan milik Anda.'], 404);
        }

        $notifikasi->delete();

        return response()->json(['message' => 'Notifikasi berhasil dihapus.'], 200);
    }

    /**
     * (Untuk Admin) Menampilkan daftar pengumuman yang pernah dikirim
     * GET /api/pengumuman
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        
        // Ambil pengumuman unik berdasarkan judul dan pesan
        // Group by untuk menghindari duplikasi per peserta
        $pengumuman = Notifikasi::select('judul', 'pesan', 'created_at')
                        ->selectRaw('MIN(id) as id, COUNT(*) as target_count')
                        ->groupBy('judul', 'pesan', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);
        
        // Transform data untuk frontend
        $pengumumanData = $pengumuman->map(function ($item) {
            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'pesan' => $item->pesan,
                'pengirim' => 'Admin', // Default sender
                'target_count' => $item->target_count,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'data' => $pengumumanData,
            'current_page' => $pengumuman->currentPage(),
            'last_page' => $pengumuman->lastPage(),
            'total' => $pengumuman->total(),
            'per_page' => $pengumuman->perPage(),
        ]);
    }

    /**
     * (Untuk Admin) Mengupdate pengumuman
     * PUT /api/pengumuman/{id}
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:1000',
            'pesan' => 'required|string|max:1000',
            'pengirim' => 'nullable|string|max:255',
        ]);

        // Cari notifikasi berdasarkan ID
        $notifikasi = Notifikasi::find($id);
        
        if (!$notifikasi) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan.'], 404);
        }

        // Update semua notifikasi dengan judul dan pesan yang sama
        $oldJudul = $notifikasi->judul;
        $oldPesan = $notifikasi->pesan;
        
        $updated = Notifikasi::where('judul', $oldJudul)
                            ->where('pesan', $oldPesan)
                            ->update([
                                'judul' => $validatedData['judul'],
                                'pesan' => $validatedData['pesan'],
                                'updated_at' => now(),
                            ]);

        return response()->json([
            'message' => "Pengumuman berhasil diperbarui untuk {$updated} peserta.",
            'data' => [
                'id' => $id,
                'judul' => $validatedData['judul'],
                'pesan' => $validatedData['pesan'],
                'pengirim' => $validatedData['pengirim'] ?? 'Admin',
            ]
        ]);
    }

    /**
     * (Untuk Admin) Menghapus pengumuman
     * DELETE /api/pengumuman/{id}
     */
    public function destroy($id)
    {
        // Cari notifikasi berdasarkan ID
        $notifikasi = Notifikasi::find($id);
        
        if (!$notifikasi) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan.'], 404);
        }

        // Hapus semua notifikasi dengan judul dan pesan yang sama
        $oldJudul = $notifikasi->judul;
        $oldPesan = $notifikasi->pesan;
        
        $deleted = Notifikasi::where('judul', $oldJudul)
                            ->where('pesan', $oldPesan)
                            ->delete();

        return response()->json([
            'message' => "Pengumuman berhasil dihapus untuk {$deleted} peserta."
        ]);
    }
}