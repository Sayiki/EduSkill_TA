<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\Admin; // Pastikan model Admin di-import
use Illuminate\Http\Request;
use App\Http\Resources\PelatihanResource; // Import resource
use Illuminate\Validation\Rule;

class PelatihanController extends Controller
{
    /**
     * Menampilkan daftar semua pelatihan (publik).
     * GET /api/pelatihan
     */
    public function index()
    {
        $pelatihan = Pelatihan::with(['mentor', 'admin.user'])
            ->latest()
            ->get();
        return PelatihanResource::collection($pelatihan);
    }

    /**
     * Menyimpan pelatihan baru (hanya Admin).
     * POST /api/pelatihan
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pelatihan'       => 'required|string|max:100',
            'keterangan_pelatihan' => 'required|string|max:350',
            'kategori'             => 'required|string|max:100',
            'biaya'                => 'required|string',
            'jumlah_kuota'         => 'required|integer|min:1',
            'waktu_pengumpulan'    => 'required|date_format:Y-m-d H:i:s',
            'mentor_id'            => 'nullable|integer|exists:mentor,id',
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;
        $validatedData['jumlah_peserta'] = 0; // Default jumlah peserta awal

        $pelatihan = Pelatihan::create($validatedData);

        return new PelatihanResource($pelatihan->load(['mentor', 'admin.user']));
    }

    /**
     * Menampilkan detail pelatihan spesifik (publik).
     * GET /api/pelatihan/{id}
     */
    public function show($id)
    {
        $pelatihan = Pelatihan::with(['mentor', 'admin.user'])->find($id);
        if (!$pelatihan) {
            return response()->json(['message' => 'Pelatihan tidak ditemukan'], 404);
        }
        return new PelatihanResource($pelatihan);
    }

    /**
     * Memperbarui pelatihan yang ada (hanya Admin).
     * PUT /api/pelatihan/{id}
     */
    public function update(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $validatedData = $request->validate([
            'nama_pelatihan'       => 'required|string|max:100',
            'keterangan_pelatihan' => 'required|string|max:350',
            'kategori'             => 'required|string|max:100',
            'biaya'                => 'required|string',
            'jumlah_kuota'         => 'required|integer|min:1',
            'waktu_pengumpulan'    => 'required|date_format:Y-m-d H:i:s',
            'mentor_id'            => 'nullable|integer|exists:mentor,id',
        ]);

        // admin_id (pembuat asli) tidak diubah saat update.
        // jumlah_peserta diupdate oleh sistem saat pendaftaran diterima/dibatalkan.

        $pelatihan->update($validatedData);

        return new PelatihanResource($pelatihan->fresh()->load(['mentor', 'admin.user']));
    }

    /**
     * Menghapus pelatihan (hanya Admin).
     * DELETE /api/pelatihan/{id}
     */
    public function destroy($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        
        // Pertimbangkan apa yang terjadi dengan pendaftaran terkait jika pelatihan dihapus.
        // onDelete('cascade') pada tabel daftar_pelatihan.id_pelatihan akan menghapus semua pendaftaran.
        // Jika ada peserta yang sudah diterima, mungkin perlu logika tambahan.
        
        $pelatihan->delete();

        return response()->json(['message' => 'Pelatihan berhasil dihapus'], 200);
    }
}
