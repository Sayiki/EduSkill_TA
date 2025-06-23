<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanAdmin;
use App\Models\Admin; // Make sure Admin model is correctly namespaced if used directly
use Illuminate\Http\Request;
use App\Http\Resources\LaporanAdminResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // This line is correct

class LaporanAdminController extends Controller
{
    use AuthorizesRequests; // This correctly uses the trait

    /**
     * Menampilkan daftar semua laporan admin (untuk Ketua).
     * GET /api/laporan-admin
     */
    public function index()
    {
        $laporan = LaporanAdmin::with('admin.user')->latest()->get();
        return LaporanAdminResource::collection($laporan);
    }

    /**
     * Menampilkan laporan admin spesifik (untuk Ketua).
     */
    public function showLaporanByIdForKetua($laporan_id)
    {
        $laporan = LaporanAdmin::with('admin.user')->find($laporan_id);
        if (!$laporan) {
            return response()->json(['message' => 'Laporan Admin tidak ditemukan'], 404);
        }

        return new LaporanAdminResource($laporan);
    }

    /**
     * Admin membuat atau memperbarui laporannya sendiri.
     * POST /api/my-laporan-admin
     */
    public function storeOrUpdateMyLaporan(Request $request)
    {
        $loggedInUser = Auth::user();
        // Ensure adminProfile relation exists and is loaded correctly on User model
        if (!$loggedInUser || !$loggedInUser->adminProfile) { 
            return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;

 

        $validatedData = $request->validate([
            'jumlah_peserta'         => 'required|integer|min:0',
            'jumlah_lulusan_bekerja' => 'required|integer|min:0',
            'jumlah_pendaftar'       => 'required|integer|min:0',
            'pelatihan_dibuka'       => 'required|string|max:100',
            'pelatihan_berjalan'     => 'required|string|max:100',
        ]);

        $validatedData['admin_id'] = $admin->id;
        $validatedData['waktu_upload'] = now();

        $laporan = LaporanAdmin::updateOrCreate(
            ['admin_id' => $admin->id],
            $validatedData
        );

        return new LaporanAdminResource($laporan->load('admin.user'));
    }

    /**
     * Admin melihat laporannya sendiri.
     * GET /api/my-laporan-admin
     */
    public function showMyLaporan(Request $request)
    {
        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
            return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;

        $laporan = LaporanAdmin::with('admin.user')->where('admin_id', $admin->id)->first();

        if (!$laporan) {
            return response()->json(['message' => 'Anda belum membuat laporan.'], 404);
        }

        return new LaporanAdminResource($laporan);
    }

    public function destroy(Request $request, $laporan_id) 
    {
        $laporan = LaporanAdmin::find($laporan_id);

        if (!$laporan) {
            return response()->json(['message' => 'Laporan Admin tidak ditemukan'], 404);
        }

        $laporan->delete();

        return response()->json(['message' => 'Laporan Admin berhasil dihapus'], 200);
    }
}
