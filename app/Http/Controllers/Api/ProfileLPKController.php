<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileLPK;
use App\Models\InformasiLembaga; // Import InformasiLembaga
use Illuminate\Http\Request;
use App\Http\Resources\ProfileLPKResource; // Import resource
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Untuk debugging

class ProfileLPKController extends Controller
{
    /**
     * Menampilkan informasi profile LPK utama (publik).
     * Kita asumsikan hanya ada satu entri LPK.
     */
    public function index(Request $request)
    {
        $profile = ProfileLPK::with('lembaga')->latest()->first(); 

        if (!$profile) {
            return response()->json(['data' => null, 'message' => 'Profil LPK belum diatur.'], 200); 
        }
        return new ProfileLPKResource($profile);
    }

    /**
     * Menyimpan atau memperbarui informasi profile LPK (hanya Admin).
     * Ini akan bertindak sebagai "upsert".
     * lembaga_id akan diambil secara otomatis.
     */
    public function store(Request $request)
    {
        $informasiLembaga = InformasiLembaga::first();

        if (!$informasiLembaga) {
            return response()->json(['message' => 'Informasi Lembaga Induk belum ada. Silakan buat terlebih dahulu.'], 400);
        }

        // 1. FIX THE VALIDATION: Change 'string' to 'image' for file uploads.
        $validatedData = $request->validate([
            'nama_lpk'      => 'required|string|max:255',
            'deskripsi_lpk' => 'required|string',
            'foto_lpk'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Accepts images up to 2MB
        ]);
        
        $validatedData['lembaga_id'] = $informasiLembaga->id;
        
        $profile = ProfileLPK::first(); 

        // 2. HANDLE THE FILE UPLOAD
        if ($request->hasFile('foto_lpk')) {
            // If a profile already exists and has an old photo, delete it.
            if ($profile && $profile->foto_lpk) {
                Storage::disk('public')->delete($profile->foto_lpk);
            }
            
            // Store the new photo and get its path.
            $path = $request->file('foto_lpk')->store('profile-lpk', 'public');
            
            // Save the new photo's path to be stored in the database.
            $validatedData['foto_lpk'] = $path;
        }

        // Update the profile if it exists, otherwise create a new one.
        if ($profile) {
            $profile->update($validatedData);
        } else {
            $profile = ProfileLPK::create($validatedData);
        }

        // Return the fresh data from the database.
        return new ProfileLPKResource($profile->fresh()->load('lembaga'));
    }

    /**
     * Menampilkan detail profile LPK spesifik (publik).
     * Umumnya tidak diperlukan jika hanya ada satu entri.
     */
    public function show($id)
    {
        $profile = ProfileLPK::with('lembaga')->find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profil LPK tidak ditemukan'], 404);
        }
        return new ProfileLPKResource($profile);
    }

    /**
     * Memperbarui informasi profile LPK yang ada (hanya Admin).
     * Dengan "upsert" di store(), ini menjadi redundant.
     */
    public function update(Request $request, $id)
    {
        $profile = ProfileLPK::findOrFail($id); 

        $informasiLembaga = InformasiLembaga::first();
        if (!$informasiLembaga) {
            return response()->json(['message' => 'Informasi Lembaga Induk belum ada.'], 400);
        }

        $validatedData = $request->validate([
            'nama_lpk'      => 'sometimes|required|string|max:255',
            'deskripsi_lpk' => 'sometimes|required|string',
            'foto_lpk'      => 'sometimes|nullable|string|max:10000',
        ]);

        $validatedData['lembaga_id'] = $informasiLembaga->id;

        if ($request->filled('foto_lpk') && isset($validatedData['foto_lpk']) && $validatedData['foto_lpk'] !== $profile->foto_lpk) {
            if ($profile->foto_lpk && !filter_var($profile->foto_lpk, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($profile->foto_lpk)) {
                     Storage::disk('public')->delete($profile->foto_lpk);
                }
            }
        }  elseif ($request->has('foto_lpk') && isset($validatedData['foto_lpk']) && is_null($validatedData['foto_lpk'])) {
            if ($profile->foto_lpk && !filter_var($profile->foto_lpk, FILTER_VALIDATE_URL)) {
                 if (Storage::disk('public')->exists($profile->foto_lpk)) {
                    Storage::disk('public')->delete($profile->foto_lpk);
                }
            }
        }
        
        $profile->update($validatedData);
        return new ProfileLPKResource($profile->fresh()->load('lembaga'));
    }

    /**
     * Menghapus informasi profile LPK (hanya Admin).
     * Sebaiknya TIDAK ADA jika informasi ini harus selalu ada.
     */
    public function destroy($id)
    {
        $profile = ProfileLPK::findOrFail($id);
        
        if ($profile->foto_lpk && !filter_var($profile->foto_lpk, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($profile->foto_lpk)) {
                Storage::disk('public')->delete($profile->foto_lpk);
            }
        }
        
        $profile->delete();

        return response()->json(['message' => 'Profil LPK berhasil dihapus'], 200);
    }
}
