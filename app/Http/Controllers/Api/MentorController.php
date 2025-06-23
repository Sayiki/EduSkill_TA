<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Admin; // Pastikan model Admin di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MentorResource;

class MentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentors = Mentor::latest()->get();
        return MentorResource::collection($mentors);
    }

    /**
     * Menyimpan mentor baru (hanya Admin).
     * POST /api/mentor
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_mentor' => 'required|string|max:255',
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;


        $mentor = Mentor::create($validatedData);

        return new MentorResource($mentor);
    }

    /**
     * Menampilkan detail mentor spesifik (publik).
     * GET /api/mentor/{id}
     */
    public function show($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json(['message' => 'Mentor tidak ditemukan'], 404);
        }
        return new MentorResource($mentor);
    }

    /**
     * Memperbarui mentor yang ada (hanya Admin).
     * POST /api/mentor/{id} (dengan _method: 'PUT')
     */
    public function update(Request $request, $id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json(['message' => 'Mentor tidak ditemukan'], 404);
        }

        $validatedData = $request->validate([
            'nama_mentor' => 'sometimes|required|string|max:255',
        ]);

        
        $mentor->update($validatedData);

        return new MentorResource($mentor->fresh());
    }

    /**
     * Menghapus mentor (hanya Admin).
     * DELETE /api/mentor/{id}
     */
    public function destroy($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json(['message' => 'Mentor tidak ditemukan'], 404);
        }


        // Perhatian: Jika mentor dihapus, apa yang terjadi dengan pelatihan yang menunjuk ke mentor_id ini?
        // Migrasi kita mengatur onDelete('set null') untuk pelatihan.mentor_id, jadi ini aman.
        $mentor->delete();

        return response()->json(['message' => 'Mentor berhasil dihapus'], 200);
    }
}
