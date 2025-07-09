<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Admin; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 
use Illuminate\Validation\Rule;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $berita = Berita::paginate($perPage);

        return response()->json($berita);
    }

    /**
     * Menyimpan berita baru (hanya Admin).
     * POST /api/berita
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', 
        ]);

        // Dapatkan profil Admin dari User yang login
        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini atau pengguna tidak login.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;

        if ($request->hasFile('gambar')) {
            $validatedData['gambar'] = $request->file('gambar')->store('berita_gambar', 'public');
        }

        $berita = Berita::create($validatedData);

        return response()->json($berita->load('adminProfile.user'), 201);
    }

    /**
     * Menampilkan detail berita spesifik (publik).
     * GET /api/berita/{id}
     */
    public function show($id)
    {
        $berita = Berita::findOrFail($id);

        return response()->json([
            'data' => $berita
        ]);
    }

    /**
     * Memperbarui berita yang ada (hanya Admin).
     * POST /api/berita/{id} (dengan _method: 'PUT') atau PUT /api/berita/{id}
     */
    public function update(Request $request, $id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita tidak ditemukan'], 404);
        }

        // Otorisasi tambahan: Pastikan admin yang login yang mengupdate,
        // atau admin mana saja boleh (tergantung kebijakan Anda)
        // Untuk contoh ini, kita asumsikan admin mana saja boleh mengupdate.

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'date' => 'sometimes|required|date_format:Y-m-d',
            'gambar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $validatedData['gambar'] = $request->file('gambar')->store('berita_gambar', 'public');
        } elseif ($request->input('remove_gambar') == true && $berita->gambar) {
            // Jika ada flag untuk menghapus gambar dan gambar ada
             if (Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $validatedData['gambar'] = null;
        }


        $berita->update($validatedData);

        return response()->json($berita->fresh()->load('adminProfile.user'));
    }

    /**
     * Menghapus berita (hanya Admin).
     * DELETE /api/berita/{id}
     */
    public function destroy($id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita tidak ditemukan'], 404);
        }

        // Otorisasi tambahan jika perlu

        // Hapus gambar dari storage jika ada
        if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return response()->json(['message' => 'Berita berhasil dihapus'], 200);
    }
}
