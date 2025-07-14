<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Admin; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MentorResource;
use Illuminate\Database\Eloquent\Builder; 

class MentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
    {
        // 1. Ambil parameter dari query string
        $perPage = $request->query('per_page', 10);
        $searchTerm = $request->query('search');

        // 2. Mulai membangun query dengan eager loading
        $query = Mentor::with(['user']);

        // 3. Terapkan filter pencarian jika ada
        if ($searchTerm) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereHas('user', function (Builder $q_user) use ($searchTerm) {
                    $q_user->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('bidang_keahlian', 'like', '%' . $searchTerm . '%');
            });
        }

        $query->latest();

        // 4. Paginate the results
        $paginator = $query->paginate($perPage);

        // 5. Return the paginated response (Laravel automatically formats this correctly)
        return response()->json($paginator);
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
