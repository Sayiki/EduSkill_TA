<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\Admin;
use App\Models\DaftarPelatihan; // Import DaftarPelatihan model
use App\Models\Peserta;
use Illuminate\Http\Request;
use App\Http\Resources\PelatihanResource; // Import resource
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Import DB facade for transactions
use Illuminate\Support\Facades\Log; // Optional: for logging
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class PelatihanController extends Controller
{
    /**
     * Menampilkan daftar semua pelatihan (publik).
     * GET /api/pelatihan
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $searchQuery = $request->query('search');
        $statusFilter = $request->query('status_pelatihan');
        $postStatusFilter = $request->query('post_status');

        $query = Pelatihan::with(['mentor', 'admin.user', 'kategori']);

        if ($searchQuery) {
            $query->where('nama_pelatihan', 'like', '%' . $searchQuery . '%');
        }

        if ($statusFilter && $statusFilter !== 'Semua') {
            $query->where('status_pelatihan', $statusFilter);
        }

        if ($postStatusFilter && $postStatusFilter !== 'Semua') {
            $query->where('post_status', $postStatusFilter);
        }

        $paginator = $query->paginate($perPage);

        return PelatihanResource::collection($paginator);
    }

    /**
     * Menyimpan pelatihan baru (hanya Admin).
     * POST /api/pelatihan
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pelatihan'       => 'required|string|max:100',
            'keterangan_pelatihan' => 'required|string|max:350',
            'foto_pelatihan'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'biaya'                => 'required|required|integer',
            'kategori_id'          => 'required|integer|exists:kategori_pelatihan,id',
            'jumlah_kuota'         => 'required|integer|min:1',
            'waktu_pengumpulan'    => 'required|date_format:Y-m-d H:i:s|after_or_equal:today',
            'mentor_id'            => 'nullable|integer|exists:mentor,id',
            'status_pelatihan'     => ['sometimes', Rule::in(['Belum Dimulai', 'Sedang berlangsung', 'Selesai'])],
            'post_status'          => ['sometimes', Rule::in(['Draft', 'Published'])],
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Profil admin tidak ditemukan untuk pengguna ini.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        $validatedData['admin_id'] = $admin->id;
        $validatedData['jumlah_peserta'] = 0;

        if (!isset($validatedData['status_pelatihan'])) {
            $validatedData['status_pelatihan'] = 'Belum Dimulai';
        }
        if (!isset($validatedData['post_status'])) {
            $validatedData['post_status'] = 'Draft';
        }

        $pelatihan = Pelatihan::create($validatedData);

        return new PelatihanResource($pelatihan->load(['mentor', 'admin.user']));
    }

    /**
     * Menampilkan detail pelatihan spesifik (publik).
     * GET /api/pelatihan/{id}
     */
    public function show($id)
    {
        $pelatihan = Pelatihan::with(['mentor', 'admin.user'])->find($id);
        if (!$pelatihan) {
            return response()->json(['message' => 'Pelatihan tidak ditemukan'], 404);
        }
        return new PelatihanResource($pelatihan);
    }

    /**
     * Memperbarui pelatihan yang ada (hanya Admin).
     * PUT /api/pelatihan/{id}
     */
    public function update(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $validatedData = $request->validate([
            'nama_pelatihan'       => 'sometimes|required|string|max:100',
            'keterangan_pelatihan' => 'sometimes|required|string|max:350',
            'foto_pelatihan'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'kategori_id'          => 'sometimes|required|integer|exists:kategori_pelatihan,id',
            'biaya'                => 'sometimes|required|integer',
            'jumlah_kuota'         => 'sometimes|required|integer|min:1',
            'waktu_pengumpulan'    => 'sometimes|required|date_format:Y-m-d H:i:s|after_or_equal:today',
            'mentor_id'            => 'sometimes|nullable|integer|exists:mentor,id',
            'status_pelatihan'     => ['sometimes', 'required', Rule::in(['Belum Dimulai', 'Sedang berlangsung', 'Selesai'])],
            'post_status'          => ['sometimes', 'required', Rule::in(['Draft', 'Published'])],
        ]);

        if ($request->hasFile('foto_pelatihan')) {
            // Hapus foto lama jika ada
            if ($pelatihan->foto_pelatihan) {
                Storage::disk('public')->delete($pelatihan->foto_pelatihan);
            }
            // Simpan foto baru dan dapatkan path-nya
            $path = $request->file('foto_pelatihan')->store('gambar_pelatihan', 'public');
            $validatedData['foto_pelatihan'] = $path;
        }

        // Capture the old status before updating
        $oldStatusPelatihan = $pelatihan->status_pelatihan;

        DB::transaction(function () use ($pelatihan, $validatedData, $oldStatusPelatihan) {
            $pelatihan->update($validatedData);

            // Check if status_pelatihan was changed to 'Selesai'
            if (isset($validatedData['status_pelatihan']) && $validatedData['status_pelatihan'] === 'Selesai' && $oldStatusPelatihan !== 'Selesai') {
                // Get all accepted participants for this training
                $acceptedParticipantIds = DaftarPelatihan::where('pelatihan_id', $pelatihan->id)
                                                         ->where('status', 'diterima')
                                                         ->pluck('peserta_id');

                if ($acceptedParticipantIds->isNotEmpty()) {
                    // Update their status_lulus to 'Lulus'
                    Peserta::whereIn('id', $acceptedParticipantIds)
                           ->update(['status_lulus' => 'Lulus']);
                    Log::info("PelatihanController: Updated status_lulus to 'Lulus' for participants of Pelatihan ID: {$pelatihan->id}"); // Optional logging
                }
            }
        });

        return new PelatihanResource($pelatihan->fresh()->load(['mentor', 'admin.user']));
    }

    /**
     * Menghapus pelatihan (hanya Admin).
     * DELETE /api/pelatihan/{id}
     */
    public function destroy($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        
        // Hapus foto terkait dari storage sebelum menghapus record
        if ($pelatihan->foto_pelatihan) {
            Storage::disk('public')->delete($pelatihan->foto_pelatihan);
        }
        
        $pelatihan->delete();

        return response()->json(['message' => 'Pelatihan berhasil dihapus'], 200);
    }
}
