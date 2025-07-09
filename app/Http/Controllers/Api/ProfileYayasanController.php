<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileYayasan;
use App\Models\Admin; // Pastikan di-import
use Illuminate\Http\Request;
use App\Http\Resources\ProfileYayasanResource; // Import resource
use Illuminate\Support\Facades\Storage; // Untuk menghapus file lama jika diperlukan

class ProfileYayasanController extends Controller
{
    /**
     * Menampilkan informasi profile yayasan utama (publik).
     * Kita asumsikan hanya ada satu entri.
     */
    public function index(Request $request)
    {
        $profile = ProfileYayasan::latest()->first(); 

        if (!$profile) {
            return response()->json(['data' => null, 'message' => 'Profil yayasan belum diatur.'], 200); 
        }
        return new ProfileYayasanResource($profile);
    }

    /**
     * Menyimpan atau memperbarui informasi profile yayasan (hanya Admin).
     * Ini akan bertindak sebagai "upsert".
     */
    public function store(Request $request)
    {
        // 1. FIX THE VALIDATION: Change 'string' to 'image' for file uploads.
        $validatedData = $request->validate([
            'nama_yayasan'      => 'required|string|min:5|max:100',
            'deskripsi_yayasan' => 'required|string|min:5|max:350',
            'foto_yayasan'      => 'nullable|image|mimes:jpg,jpeg,png|max:5120', 
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
            return response()->json(['message' => 'Akses ditolak atau profil admin tidak ditemukan.'], 403);
        }
        $admin = $loggedInUser->adminProfile;

        // Find the first profile entry, or create a new one if it doesn't exist.
        // This is a more concise way to handle both create and update.
        $profile = ProfileYayasan::firstOrNew();

        // 2. HANDLE THE FILE UPLOAD
        if ($request->hasFile('foto_yayasan')) {
            // If the profile already exists and has an old photo, delete it.
            if ($profile->exists && $profile->foto_yayasan) {
                Storage::disk('public')->delete($profile->foto_yayasan);
            }
            
            // Store the new photo and get its path.
            $path = $request->file('foto_yayasan')->store('profile-yayasan', 'public');
            
            // Save the new photo's path to be stored in the database.
            $validatedData['foto_yayasan'] = $path;
        }

        // Fill the profile model with validated data and admin_id
        $profile->fill($validatedData);
        $profile->admin_id = $admin->id;
        $profile->save();
        
        // Return the fresh data from the database.
        return new ProfileYayasanResource($profile->fresh());
    }

    /**
     * Menampilkan detail profile yayasan spesifik (publik).
     * Umumnya tidak diperlukan jika hanya ada satu entri, index() sudah cukup.
     */
    public function show($id)
    {
        $profile = ProfileYayasan::find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profil yayasan tidak ditemukan'], 404);
        }
        return new ProfileYayasanResource($profile);
    }

    /**
     * Memperbarui informasi profile yayasan yang ada (hanya Admin).
     * Dengan "upsert" di store(), ini menjadi redundant kecuali ingin endpoint PUT terpisah.
     */
    public function update(Request $request, $id)
    {
        $profile = ProfileYayasan::findOrFail($id); // Atau firstOrFail()

        $validatedData = $request->validate([
            'nama_yayasan'      => 'sometimes|required|string|max:255',
            'deskripsi_yayasan' => 'sometimes|required|string',
            'foto_yayasan'      => 'sometimes|nullable|string|max:5120', // Jika URL, bisa 'url' rule
        ]);
        
        // Logika untuk menghapus foto lama jika foto baru (string path/URL) diberikan
        if ($request->filled('foto_yayasan') && $validatedData['foto_yayasan'] !== $profile->foto_yayasan) {
            if ($profile->foto_yayasan && !filter_var($profile->foto_yayasan, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($profile->foto_yayasan)) {
                    Storage::disk('public')->delete($profile->foto_yayasan);
                }
            }
        } elseif ($request->has('foto_yayasan') && is_null($validatedData['foto_yayasan'])) {
            // Jika dikirim foto_yayasan: null, hapus foto lama jika ada dan bukan URL
            if ($profile->foto_yayasan && !filter_var($profile->foto_yayasan, FILTER_VALIDATE_URL)) {
                 if (Storage::disk('public')->exists($profile->foto_yayasan)) {
                    Storage::disk('public')->delete($profile->foto_yayasan);
                }
            }
        }

        $profile->update($validatedData);

        return new ProfileYayasanResource($profile->fresh());
    }

    /**
     * Menghapus informasi profile yayasan (hanya Admin).
     * Sebaiknya TIDAK ADA jika informasi ini harus selalu ada.
     */
    public function destroy($id)
    {
        $profile = ProfileYayasan::findOrFail($id);
        
        // Hapus foto dari storage jika itu adalah path lokal
        if ($profile->foto_yayasan && !filter_var($profile->foto_yayasan, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($profile->foto_yayasan)) {
                Storage::disk('public')->delete($profile->foto_yayasan);
            }
        }
        
        $profile->delete();

        return response()->json(['message' => 'Profil yayasan berhasil dihapus'], 200);
    }
}
