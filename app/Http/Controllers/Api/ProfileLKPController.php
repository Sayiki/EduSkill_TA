<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileLKP;
use App\Models\InformasiLembaga; // Import InformasiLembaga
use Illuminate\Http\Request;
use App\Http\Resources\ProfileLKPResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Untuk debugging

class ProfileLKPController extends Controller
{
    /**
     * Menampilkan informasi profile LKP utama (publik).
     * Kita asumsikan hanya ada satu entri LKP.
     */
    public function index(Request $request)
    {
        $profile = ProfileLKP::with('lembaga')->latest()->first(); 

        if (!$profile) {
            return response()->json(['data' => null, 'message' => 'Profil LKP belum diatur.'], 200); 
        }
        return new ProfileLKPResource($profile);
    }

    /**
     * Menyimpan atau memperbarui informasi profile LKP (hanya Admin).
     * Ini akan bertindak sebagai "upsert".
     * lembaga_id akan diambil secara otomatis.
     */
    public function store(Request $request)
    {
        // Ambil satu-satunya entri InformasiLembaga
        $informasiLembaga = InformasiLembaga::first();

        if (!$informasiLembaga) {
            return response()->json(['message' => 'Informasi Lembaga Induk belum ada. Silakan buat terlebih dahulu.'], 400);
        }

        $validatedData = $request->validate([
            // 'lembaga_id' tidak lagi divalidasi dari request
            'nama_lkp'      => 'required|string|max:255',
            'deskripsi_lkp' => 'required|string',
            'foto_lkp'      => 'nullable|string|max:2048', // Asumsi string URL/path
        ]);
        
        // Tambahkan lembaga_id secara otomatis
        $validatedData['lembaga_id'] = $informasiLembaga->id;
        
        $profile = ProfileLKP::first(); // Asumsi hanya ada satu profil LKP

        // Logika untuk foto
        if ($profile) {
            if ($request->filled('foto_lkp') && $profile->foto_lkp && 
                (isset($validatedData['foto_lkp']) && $validatedData['foto_lkp'] !== $profile->foto_lkp) && 
                !filter_var($profile->foto_lkp, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($profile->foto_lkp)) {
                    Storage::disk('public')->delete($profile->foto_lkp);
                }
            } elseif ($request->has('foto_lkp') && isset($validatedData['foto_lkp']) && is_null($validatedData['foto_lkp']) && $profile->foto_lkp && !filter_var($profile->foto_lkp, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($profile->foto_lkp)) {
                    Storage::disk('public')->delete($profile->foto_lkp);
                }
            }
            $profile->update($validatedData);
        } else {
            $profile = ProfileLKP::create($validatedData);
        }
 
        return new ProfileLKPResource($profile->fresh()->load('lembaga'));
    }

    /**
     * Menampilkan detail profile LKP spesifik (publik).
     */
    public function show($id)
    {
        $profile = ProfileLKP::with('lembaga')->find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profil LKP tidak ditemukan'], 404);
        }
        return new ProfileLKPResource($profile);
    }

    /**
     * Memperbarui informasi profile LKP yang ada (hanya Admin).
     */
    public function update(Request $request, $id)
    {
        $profile = ProfileLKP::findOrFail($id); 

        // Ambil satu-satunya entri InformasiLembaga
        $informasiLembaga = InformasiLembaga::first();
        if (!$informasiLembaga) {
            return response()->json(['message' => 'Informasi Lembaga Induk belum ada.'], 400);
        }

        $validatedData = $request->validate([
            // 'lembaga_id' tidak lagi divalidasi dari request
            'nama_lkp'      => 'sometimes|required|string|max:255',
            'deskripsi_lkp' => 'sometimes|required|string',
            'foto_lkp'      => 'sometimes|nullable|string|max:2048',
        ]);

        // Tambahkan lembaga_id secara otomatis
        $validatedData['lembaga_id'] = $informasiLembaga->id;

        // Logika hapus foto lama
        if ($request->filled('foto_lkp') && isset($validatedData['foto_lkp']) && $validatedData['foto_lkp'] !== $profile->foto_lkp) {
            if ($profile->foto_lkp && !filter_var($profile->foto_lkp, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($profile->foto_lkp)) {
                     Storage::disk('public')->delete($profile->foto_lkp);
                }
            }
        }  elseif ($request->has('foto_lkp') && isset($validatedData['foto_lkp']) && is_null($validatedData['foto_lkp'])) {
            if ($profile->foto_lkp && !filter_var($profile->foto_lkp, FILTER_VALIDATE_URL)) {
                 if (Storage::disk('public')->exists($profile->foto_lkp)) {
                    Storage::disk('public')->delete($profile->foto_lkp);
                }
            }
        }
        
        $profile->update($validatedData);
        return new ProfileLKPResource($profile->fresh()->load('lembaga'));
    }

    /**
     * Menghapus informasi profile LKP (hanya Admin).
     */
    public function destroy($id)
    {
        $profile = ProfileLKP::findOrFail($id);
        
        if ($profile->foto_lkp && !filter_var($profile->foto_lkp, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($profile->foto_lkp)) {
                Storage::disk('public')->delete($profile->foto_lkp);
            }
        }
        
        $profile->delete();

        return response()->json(['message' => 'Profil LKP berhasil dihapus'], 200);
    }
}
