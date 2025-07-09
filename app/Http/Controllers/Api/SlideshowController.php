<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use App\Models\Admin; // Pastikan model Admin di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SlideshowResource;

class SlideshowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $slideshows = Slideshow::latest()->get();

        return SlideshowResource::collection($slideshows);
    }

    /**
     * Menyimpan slideshow baru (hanya Admin).
     * POST /api/slideshow
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_slide' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 2MB
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;

        if ($request->hasFile('gambar')) {
            $validatedData['gambar'] = $request->file('gambar')->store('slideshow_gambar', 'public');
        }

        $slideshow = Slideshow::create($validatedData);

        return new SlideshowResource($slideshow);
    }

    /**
     * Menampilkan detail slideshow spesifik (publik).
     * GET /api/slideshow/{id}
     */
    public function show($id)
    {
        $slideshow = Slideshow::find($id);
        if (!$slideshow) {
            return response()->json(['message' => 'Slideshow tidak ditemukan'], 404);
        }
        return new SlideshowResource($slideshow);
    }

    /**
     * Memperbarui slideshow yang ada (hanya Admin).
     * POST /api/slideshow/{id} (dengan _method: 'PUT')
     */
    public function update(Request $request, $id)
    {
        $slideshow = Slideshow::find($id);
        if (!$slideshow) {
            return response()->json(['message' => 'Slideshow tidak ditemukan'], 404);
        }

        $validatedData = $request->validate([
            'nama_slide' => 'sometimes|required|string|max:255',
            'gambar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('gambar')) {
            if ($slideshow->gambar && Storage::disk('public')->exists($slideshow->gambar)) {
                Storage::disk('public')->delete($slideshow->gambar);
            }
            $validatedData['gambar'] = $request->file('gambar')->store('slideshow_gambar', 'public');
        } elseif ($request->input('remove_gambar') == true && $slideshow->gambar) {
             if (Storage::disk('public')->exists($slideshow->gambar)) {
                Storage::disk('public')->delete($slideshow->gambar);
            }
            $validatedData['gambar'] = null;
        }
        
        $slideshow->update($validatedData);

        return new SlideshowResource($slideshow->fresh());
    }

    /**
     * Menghapus slideshow (hanya Admin).
     * DELETE /api/slideshow/{id}
     */
    public function destroy($id)
    {
        $slideshow = Slideshow::find($id);
        if (!$slideshow) {
            return response()->json(['message' => 'Slideshow tidak ditemukan'], 404);
        }

        if ($slideshow->gambar && Storage::disk('public')->exists($slideshow->gambar)) {
            Storage::disk('public')->delete($slideshow->gambar);
        }

        $slideshow->delete();

        return response()->json(['message' => 'Slideshow berhasil dihapus'], 200);
    }
}
