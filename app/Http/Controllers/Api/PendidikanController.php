<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use App\Http\Resources\PendidikanResource; // Import resource
use Illuminate\Validation\Rule; // Untuk validasi unik jika diperlukan

class PendidikanController extends Controller
{
    /**
     * Menampilkan daftar semua tingkat pendidikan (publik).
     * GET /api/pendidikan
     */
    public function index()
    {
        $pendidikan = Pendidikan::orderBy('nama_pendidikan', 'asc')->get();
        return PendidikanResource::collection($pendidikan);
    }

    /**
     * Menyimpan tingkat pendidikan baru (hanya Admin).
     * POST /api/pendidikan
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Tambahkan Rule::unique jika nama pendidikan harus unik
            'nama_pendidikan' => ['required', 'string', 'max:255', Rule::unique('pendidikan', 'nama_pendidikan')],
        ]);

        $pendidikan = Pendidikan::create($validatedData);

        return response()->json([
            'message' => 'Tingkat pendidikan berhasil dibuat.',
            'data'    => new PendidikanResource($pendidikan),
        ], 201);
    }

    /**
     * Menampilkan detail tingkat pendidikan spesifik (publik).
     * GET /api/pendidikan/{id}
     */
    public function show($id)
    {
        $pendidikan = Pendidikan::find($id);
        if (!$pendidikan) {
            return response()->json(['message' => 'Tingkat pendidikan tidak ditemukan'], 404);
        }
        return new PendidikanResource($pendidikan);
    }

    /**
     * Memperbarui tingkat pendidikan yang ada (hanya Admin).
     * PUT /api/pendidikan/{id}
     */
    public function update(Request $request, $id)
    {
        $pendidikan = Pendidikan::findOrFail($id);

        $validatedData = $request->validate([
            // Gunakan 'sometimes' agar hanya divalidasi jika field dikirim
            // Tambahkan Rule::unique jika nama pendidikan harus unik dan abaikan ID saat ini
            'nama_pendidikan' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('pendidikan', 'nama_pendidikan')->ignore($pendidikan->id)],
        ]);

        $pendidikan->update($validatedData);

        return response()->json([
            'message' => 'Tingkat pendidikan berhasil diperbarui.',
            'data'    => new PendidikanResource($pendidikan->fresh()),
        ]);
    }

    /**
     * Menghapus tingkat pendidikan (hanya Admin).
     * DELETE /api/pendidikan/{id}
     */
    public function destroy($id)
    {
        $pendidikan = Pendidikan::findOrFail($id);

        $pendidikan->delete();

        return response()->json(['message' => 'Tingkat pendidikan berhasil dihapus.'], 200);
    }
}
