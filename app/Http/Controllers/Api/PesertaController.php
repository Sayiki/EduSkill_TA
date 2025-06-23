<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Illuminate\Support\Facades\Hash; // Jika ada update password
use Illuminate\Support\Facades\Storage; // Untuk file upload
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\PesertaPublicResource;

class PesertaController extends Controller
{
    // ✅ GET /api/peserta
    public function index(Request $request)  
    {
        $semuaPeserta = Peserta::with(['user', 'pendidikan'])
        ->get(); 

    return response()->json($semuaPeserta);
    }

    public function store(Request $request)
    {
        // Validasi untuk Peserta baru
        $data = $request->validate([
            // Anda mungkin ingin memvalidasi field User juga di sini jika Admin membuat User baru
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:peserta,user_id'],
            'nik_peserta' => ['nullable', 'string', 'digits:16', Rule::unique('peserta', 'nik_peserta')],
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'alamat_peserta' => ['nullable', 'string', 'max:1000'],
            'nomor_telp' => ['nullable', 'string', 'max:20'],
            'tanggal_lahir' => ['nullable', 'date'],
            'foto_peserta' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // Max 2MB
            'status_kerja' => ['nullable', Rule::in(['bekerja', 'belum_bekerja', 'kuliah', 'wirausaha', 'tidak_diketahui'])],
            'peserta_id' => ['nullable', 'integer', 'exists:pendidikan,id'],
        ]);

        if ($request->hasFile('foto_peserta')) {
            $data['foto_peserta'] = $request->file('foto_peserta')->store('foto_profil_peserta', 'public');
        }

        $peserta = Peserta::create($data);

        return response()->json([
            'message' => 'Peserta berhasil dibuat',
            'data' => $peserta->load(['user', 'pendidikan']),
        ], 201);
    }

    public function show($id)
    {
        $peserta = Peserta::with(['user', 'pendidikan'])->find($id);

        if (!$peserta) {
            return response()->json(['message' => 'Data Peserta tidak ditemukan'], 404);
        }

        // --- BLOK OTORISASI ---
        $loggedInUser = Auth::user();
        // Jika yang mengakses adalah PESERTA, cek apakah ID peserta yang diminta adalah miliknya.
        if ($loggedInUser->peran === 'peserta' && $peserta->user_id !== $loggedInUser->id) {
            return response()->json(['message' => 'Akses ditolak. Anda hanya bisa melihat profil Anda sendiri.'], 403);
        }
        // Jika yang mengakses adalah ADMIN, dia bisa melihat profil siapa saja, jadi tidak ada pengecekan tambahan.

        return response()->json(['data' => $peserta]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        // Gunakan firstOrCreate untuk membuat profil peserta jika belum ada
        $peserta = Peserta::firstOrCreate(['user_id' => $user->id]);

        // Validasi data yang masuk
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nik_peserta' => ['sometimes', 'nullable', 'string', 'digits:16', Rule::unique('peserta')->ignore($peserta->id)],
            'jenis_kelamin' => ['sometimes', 'nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'alamat_peserta' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'nomor_telp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'tanggal_lahir' => ['sometimes', 'nullable', 'date'],
            'foto_peserta' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'pendidikan_id' => ['sometimes', 'nullable', 'integer', 'exists:pendidikan,id'],
        ]);

        try {
            DB::transaction(function () use ($request, $user, $peserta) {
                // 1. Update data di tabel 'users' jika ada
                $user->update($request->only('name', 'email'));

                // 2. Siapkan data untuk update tabel 'peserta'
                $pesertaDataToUpdate = $request->except(['name', 'email', 'foto_peserta']);

                // Handle upload file untuk foto_peserta
                if ($request->hasFile('foto_peserta')) {
                    if ($peserta->foto_peserta && Storage::disk('public')->exists($peserta->foto_peserta)) {
                        Storage::disk('public')->delete($peserta->foto_peserta);
                    }
                    $path = $request->file('foto_peserta')->store('foto_profil_peserta', 'public');
                    $pesertaDataToUpdate['foto_peserta'] = $path;
                }
                
                $peserta->update($pesertaDataToUpdate);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui profil.', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $peserta->fresh()->load(['user', 'pendidikan']),
        ]);
    }

    public function destroy($id)
    {
        $peserta = Peserta::find($id);

        $user = $peserta->user;

        if (!$peserta) {
            return response()->json(['message' => 'Data Peserta tidak ditemukan'], 404);
        }

        // Hapus foto dari storage jika ada
        if ($peserta->foto_peserta && Storage::disk('public')->exists($peserta->foto_peserta)) {
            Storage::disk('public')->delete($peserta->foto_peserta);
        }

        if ($user) {
        try {
            DB::transaction(function () use ($user, $peserta) { 
                $user->delete(); 
            });
        } catch (\Exception $e) {
            // Tangani jika ada error saat menghapus, misalnya ada constraint lain
            return response()->json(['message' => 'Gagal menghapus peserta dan user terkait.', 'error' => $e->getMessage()], 500);
        }
        } else {
            // Jika karena suatu alasan user tidak ditemukan (data tidak konsisten),
            // kita tetap bisa menghapus data peserta saja.
            $peserta->delete();
        }

        return response()->json(['message' => 'Peserta berhasil dihapus']);
    }

    public function showMyProfile(Request $request)
    {
        $user = Auth::user();

        // Ambil profil peserta yang berelasi, beserta relasi user dan pendidikan
        $peserta = Peserta::with(['user', 'pendidikan'])
                          ->where('user_id', $user->id)
                          ->first();

        if (!$peserta) {
            // Jika user ada tapi profil peserta belum dibuat, kembalikan data user dasar
            return response()->json(['data' => $user, 'message' => 'Profil peserta belum lengkap.']);
        }

        return response()->json(['data' => $peserta]);
    }

    public function getPublicProfiles(Request $request)
    {
        $pesertaHighlights = Peserta::with(['user', 'feedback'])
            
            // HANYA ambil Peserta yang MEMILIKI feedback
            // DAN di dalam feedback itu, 'tempat_kerja' tidak null.
            ->whereHas('feedback', function (Builder $query) {
                $query->whereNotNull('tempat_kerja');
            })
            
            ->whereNotNull('foto_peserta')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return PesertaPublicResource::collection($pesertaHighlights);
    }
}
