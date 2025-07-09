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
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $items = InformasiGaleri::with('admin.user')
                               ->latest()
                               ->paginate($perPage);

        return InformasiGaleriResource::collection($items);
    }

    /**
     * Menyimpan item galeri baru (hanya Admin).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul_foto' => 'required|string|min:5|max:100',
            'file_foto'   => 'required|image|mimes:jpeg,png,jpg|max:5120', 
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Akses ditolak atau profil admin tidak ditemukan.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;

        // TAMBAHKAN LOGIKA PENYIMPANAN FILE
        if ($request->hasFile('file_foto')) {
            $validatedData['file_foto'] = $request->file('file_foto')->store('galeri_kegiatan', 'public');
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
            'judul_foto' => 'sometimes|required|string|max:255',
            // UBAH VALIDASI: dari 'url' menjadi 'image' jika admin bisa ganti file
            'file_foto'   => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // TAMBAHKAN LOGIKA UPDATE FILE
        if ($request->hasFile('file_foto')) {
            // Hapus foto lama jika ada
            if ($item->file_foto && Storage::disk('public')->exists($item->file_foto)) {
                Storage::disk('public')->delete($item->file_foto);
            }
            // Simpan foto baru
            $validatedData['file_foto'] = $request->file('file_foto')->store('galeri_kegiatan', 'public');
        } elseif ($request->input('remove_file_foto') == true && $item->file_foto) {
            // Jika ada flag untuk menghapus foto dan foto ada
             if (Storage::disk('public')->exists($item->file_foto)) {
                Storage::disk('public')->delete($item->file_foto);
            }
            $validatedData['file_foto'] = null; // Set path di DB menjadi null
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
        // Jika 'file_foto' menyimpan path relatif ke file di storage lokal
        if ($item->file_foto) {
            // Cek apakah ini URL atau path. Jika bukan URL, anggap path dan coba hapus.
            if (!filter_var($item->file_foto, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($item->file_foto)) {
                    Storage::disk('public')->delete($item->file_foto);
                }
            }
            // Jika ini adalah URL eksternal, Anda mungkin tidak perlu melakukan apa-apa,
            // atau mungkin ada logika lain (misalnya, memberi tahu layanan eksternal).
        }
        
        $item->delete();

        return response()->json(['message' => 'Item galeri berhasil dihapus'], 200);
    }
}
