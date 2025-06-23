<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BannerResource;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banner = Banner::latest()->get();
        return BannerResource::collection($banner);
    }

    /**
     * Menyimpan banner baru (hanya Admin).
     * POST /api/banner
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_banner' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10000', 
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;

        if ($request->hasFile('gambar')) {
            $validatedData['gambar'] = $request->file('gambar')->store('banner_gambar', 'public');
        }

        $banner = Banner::create($validatedData);

        return new BannerResource($banner);
    }

    /**
     * Menampilkan detail banner spesifik (publik).
     * GET /api/banner/{id}
     */
    public function show($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['message' => 'Banner tidak ditemukan'], 404);
        }
        return new BannerResource($banner);
    }

    /**
     * Memperbarui banner yang ada (hanya Admin).
     * POST /api/banner/{id} (dengan _method: 'PUT')
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['message' => 'Banner tidak ditemukan'], 404);
        }

        $validatedData = $request->validate([
            'nama_banner' => 'sometimes|required|string|max:255',
            'gambar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
                Storage::disk('public')->delete($banner->gambar);
            }
            $validatedData['gambar'] = $request->file('gambar')->store('banner_gambar', 'public');
        } elseif ($request->input('remove_gambar') == true && $banner->gambar) {
             if (Storage::disk('public')->exists($banner->gambar)) {
                Storage::disk('public')->delete($banner->gambar);
            }
            $validatedData['gambar'] = null;
        }
        
        // Admin yang mengupdate tidak diubah, hanya data banner
        $banner->update($validatedData);

        return new BannerResource($banner->fresh());
    }

    /**
     * Menghapus banner (hanya Admin).
     * DELETE /api/banner/{id}
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['message' => 'Banner tidak ditemukan'], 404);
        }

        // Hapus gambar dari storage jika ada
        if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
            Storage::disk('public')->delete($banner->gambar);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner berhasil dihapus'], 200);
    }
}
