<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanAdmin;
use App\Models\Admin; // Make sure Admin model is correctly namespaced if used directly
use Illuminate\Http\Request;
use App\Http\Resources\LaporanAdminResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class LaporanAdminController extends Controller
{
    use AuthorizesRequests; // This correctly uses the trait

    /**
     * Menampilkan daftar semua laporan admin (untuk Ketua).
     * GET /api/laporan-admin
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $searchQuery = $request->query('search');

        // Start query on LaporanAdmin model, eager loading relationships
        $query = LaporanAdmin::with('admin.user');

        // Add search functionality - searches by the Admin's name
        if ($searchQuery) {
            $query->whereHas('admin.user', function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%');
            });
        }
        
        $laporan = $query->latest()->paginate($perPage);
        
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
        $admin = Auth::user()?->adminProfile;

        if (!$admin) {
            return response()->json(['message' => 'Profil admin tidak ditemukan.'], 403);
        }

    
        $validatedData = $request->validate([
            'laporan_deskripsi' => 'required|string',
            'laporan_file'      => 'nullable|file|mimes:pdf|max:5120',
        ]);
        
    
        $laporan = LaporanAdmin::firstOrNew(['admin_id' => $admin->id]);
        $laporan->laporan_deskripsi = $validatedData['laporan_deskripsi'];

     
        if ($request->hasFile('laporan_file')) {
         
            if ($laporan->laporan_file && Storage::disk('public')->exists($laporan->laporan_file)) {
                Storage::disk('public')->delete($laporan->laporan_file);
            }

          
            $filePath = $request->file('laporan_file')->store('laporan_files', 'public');
            $laporan->laporan_file = $filePath;
        }
        
        $laporan->save();

      
        return new LaporanAdminResource($laporan->load('admin.user'));
    }

    /**
     * Admin melihat laporannya sendiri.
     * GET /api/my-laporan-admin
     */
    public function showMyLaporan(Request $request)
    {
        $admin = Auth::user()?->adminProfile;

        if (!$admin) {
            return response()->json(['message' => 'Profil admin tidak ditemukan.'], 403);
        }

        $laporan = LaporanAdmin::with('admin.user')->where('admin_id', $admin->id)->first();

        if (!$laporan) {
            return response()->json(['message' => 'Anda belum membuat laporan.'], 404);
        }

        return new LaporanAdminResource($laporan);
    }

    public function destroy(Request $request, $laporan_id) 
    {
    
        $laporanAdmin = LaporanAdmin::find($laporan_id);

        if (!$laporanAdmin) {
            return response()->json(['message' => 'Laporan Admin tidak ditemukan'], 404);
        }


        if ($laporanAdmin->laporan_file && Storage::disk('public')->exists($laporanAdmin->laporan_file)) {
            Storage::disk('public')->delete($laporanAdmin->laporan_file);
        }

      
        $laporanAdmin->delete();

        return response()->json(['message' => 'Laporan Admin berhasil dihapus'], 200);
    }
}
