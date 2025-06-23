<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiGaleri;
use App\Models\Admin; // Pastikan di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Penting untuk manajemen file
use App\Http\Resources\InformasiGaleriResource; // Import resource

class InformasiGaleriController extends Controller
{
    /**
     * Menampilkan daftar semua item galeri (publik).
     */
    public function index()
    {
        $items = InformasiGaleri::with('admin.user')
            ->latest()
            ->get();
        return InformasiGaleriResource::collection($items);
    }

    /**
     * Menyimpan item galeri baru (hanya Admin).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'foto_galeri'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10000', 
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Akses ditolak atau profil admin tidak ditemukan.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;

        // TAMBAHKAN LOGIKA PENYIMPANAN FILE
        if ($request->hasFile('foto_galeri')) {
            $validatedData['foto_galeri'] = $request->file('foto_galeri')->store('galeri_kegiatan', 'public');
        }

        $item = InformasiGaleri::create($validatedData);

        return new InformasiGaleriResource($item->load('admin.user'));
    }

    /**
     * Menampilkan detail item galeri spesifik (publik).
     */
    public function show($id)
    {
        $item = InformasiGaleri::with('admin.user')->find($id);
        if (!$item) {
            return response()->json(['message' => 'Item galeri tidak ditemukan'], 404);
        }
        return new InformasiGaleriResource($item);
    }

    /**
     * Memperbarui item galeri yang ada (hanya Admin).
     */
    public function update(Request $request, $id)
    {
        $item = InformasiGaleri::findOrFail($id);

        $validatedData = $request->validate([
            'nama_kegiatan' => 'sometimes|required|string|max:255',
            // UBAH VALIDASI: dari 'url' menjadi 'image' jika admin bisa ganti file
            'foto_galeri'   => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
        ]);

        // TAMBAHKAN LOGIKA UPDATE FILE
        if ($request->hasFile('foto_galeri')) {
            // Hapus foto lama jika ada
            if ($item->foto_galeri && Storage::disk('public')->exists($item->foto_galeri)) {
                Storage::disk('public')->delete($item->foto_galeri);
            }
            // Simpan foto baru
            $validatedData['foto_galeri'] = $request->file('foto_galeri')->store('galeri_kegiatan', 'public');
        } elseif ($request->input('remove_foto_galeri') == true && $item->foto_galeri) {
            // Jika ada flag untuk menghapus foto dan foto ada
             if (Storage::disk('public')->exists($item->foto_galeri)) {
                Storage::disk('public')->delete($item->foto_galeri);
            }
            $validatedData['foto_galeri'] = null; // Set path di DB menjadi null
        }

        $item->update($validatedData);

        return new InformasiGaleriResource($item->fresh()->load('admin.user'));
    }

    /**
     * Menghapus item galeri (hanya Admin).
     */
    public function destroy($id)
    {
        $item = InformasiGaleri::findOrFail($id);

        // PASTIKAN LOGIKA PENGHAPUSAN FILE SUDAH BENAR
        // Jika 'foto_galeri' menyimpan path relatif ke file di storage lokal
        if ($item->foto_galeri) {
            // Cek apakah ini URL atau path. Jika bukan URL, anggap path dan coba hapus.
            if (!filter_var($item->foto_galeri, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($item->foto_galeri)) {
                    Storage::disk('public')->delete($item->foto_galeri);
                }
            }
            // Jika ini adalah URL eksternal, Anda mungkin tidak perlu melakukan apa-apa,
            // atau mungkin ada logika lain (misalnya, memberi tahu layanan eksternal).
        }
        
        $item->delete();

        return response()->json(['message' => 'Item galeri berhasil dihapus'], 200);
    }
}
