<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriPelatihan;
use Illuminate\Http\Request;
use App\Http\Resources\KategoriPelatihanResource; // We will create this next
use Illuminate\Validation\Rule;

class KategoriPelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/kategori-pelatihan
     */
    public function index(Request $request)
    {
        // Get query params for pagination and search
        $perPage = $request->query('per_page', 10);
        $searchQuery = $request->query('search');

        // Start the query
        $query = KategoriPelatihan::withCount('pelatihan');

        // Apply search filter if it exists
        if ($searchQuery) {
            $query->where('nama_kategori', 'like', '%' . $searchQuery . '%');
        }

        // Paginate the results
        $kategori = $query->latest()->paginate($perPage);

        // Return the paginated resource collection
        return KategoriPelatihanResource::collection($kategori);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/kategori-pelatihan
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Ensure category name is unique in the 'kategori_pelatihan' table
            'nama_kategori' => 'required|string|max:100|unique:kategori_pelatihan,nama_kategori',
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
            return response()->json(['message' => 'Akses ditolak. Profil admin tidak ditemukan.'], 403);
        }

        $admin = $loggedInUser->adminProfile;

        $kategori = KategoriPelatihan::create([
            'nama_kategori' => $validatedData['nama_kategori'],
            'admin_id' => $admin->id,
        ]);

        return new KategoriPelatihanResource($kategori);
    }

    /**
     * Display the specified resource.
     * GET /api/kategori-pelatihan/{id}
     */
    public function show(KategoriPelatihan $kategoriPelatihan)
    {
        // The route-model binding automatically finds the category or returns a 404 error
        return new KategoriPelatihanResource($kategoriPelatihan);
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/kategori-pelatihan/{id}
     */
    public function update(Request $request, KategoriPelatihan $kategoriPelatihan)
    {
        $validatedData = $request->validate([
            // When updating, the unique rule must ignore the current category's ID
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori_pelatihan')->ignore($kategoriPelatihan->id),
            ],
        ]);

        $kategoriPelatihan->update($validatedData);

        return new KategoriPelatihanResource($kategoriPelatihan);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/kategori-pelatihan/{id}
     */
    public function destroy(KategoriPelatihan $kategoriPelatihan)
    {
        // Check if any 'pelatihan' records are using this category
        if ($kategoriPelatihan->pelatihan()->exists()) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena sedang digunakan oleh satu atau lebih pelatihan.'
            ], 409); // 409 Conflict status code
        }

        $kategoriPelatihan->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus'], 200);
    }
}
