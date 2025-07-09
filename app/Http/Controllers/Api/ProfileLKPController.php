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
        $informasiLembaga = InformasiLembaga::first();

        if (!$informasiLembaga) {
            return response()->json(['message' => 'Informasi Lembaga Induk belum ada. Silakan buat terlebih dahulu.'], 400);
        }

        // 1. FIX THE VALIDATION: Change 'string' to 'image' for file uploads.
        $validatedData = $request->validate([
            'nama_lkp'      => 'required|string|min:5|max:100',
            'deskripsi_lkp' => 'required|string|min:5|max:350',
            'foto_lkp'      => 'nullable|image|mimes:jpg,jpeg,png|max:5120', 
        ]);
        
        $validatedData['lembaga_id'] = $informasiLembaga->id;
        
        $profile = Profilelkp::first(); 

        // 2. HANDLE THE FILE UPLOAD
        if ($request->hasFile('foto_lkp')) {
            // If a profile already exists and has an old photo, delete it.
            if ($profile && $profile->foto_lkp) {
                Storage::disk('public')->delete($profile->foto_lkp);
            }
            
            // Store the new photo and get its path.
            $path = $request->file('foto_lkp')->store('profile-lkp', 'public');
            
            // Save the new photo's path to be stored in the database.
            $validatedData['foto_lkp'] = $path;
        }

        // Update the profile if it exists, otherwise create a new one.
        if ($profile) {
            $profile->update($validatedData);
        } else {
            $profile = Profilelkp::create($validatedData);
        }

        // Return the fresh data from the database.
        return new ProfilelkpResource($profile->fresh()->load('lembaga'));
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
            'foto_lkp'      => 'sometimes|nullable|string|max:5120',
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
