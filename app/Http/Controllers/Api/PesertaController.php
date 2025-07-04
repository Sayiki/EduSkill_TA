<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;
use App\Models\DaftarPelatihan;
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Illuminate\Support\Facades\Hash; // Jika ada update password
use Illuminate\Support\Facades\Storage; // Untuk file upload
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\PesertaPublicResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class PesertaController extends Controller
{
    // ✅ GET /api/peserta
    public function index(Request $request) 
    {
        $perPage = $request->query('per_page', 10);
        $registrationStatus = $request->query('registration_status'); 
        $searchTerm = $request->query('search'); // <<< AKTIFKAN KEMBALI FILTER SEARCH
        $pelatihanId = $request->query('pelatihan_id'); // <<< AKTIFKAN KEMBALI FILTER PELATIHAN

        $query = Peserta::with(['user', 'pendidikan', 'daftar_pelatihan' => function($q_daftar) {
            $q_daftar->with('pelatihan'); 
        }]); 

        // Filter: Hanya Peserta yang memiliki setidaknya SATU pendaftaran dengan status 'diterima'
        if ($registrationStatus === 'diterima') {
            $query->whereHas('daftar_pelatihan', function (Builder $q_daftar_has) {
                $q_daftar_has->where('status', 'diterima');
            });
        }

        // <<< AKTIFKAN KEMBALI FILTER PELATIHAN DI BACKEND
        if ($pelatihanId) {
            $query->whereHas('daftar_pelatihan', function (Builder $q_daftar_has) use ($pelatihanId) {
                $q_daftar_has->where('pelatihan_id', $pelatihanId)
                             ->where('status', 'diterima'); // Pastikan ini konsisten dengan filter status
            });
        }

        // <<< AKTIFKAN KEMBALI FILTER SEARCH DI BACKEND
        if ($searchTerm) {
            $query->whereHas('user', function (Builder $q_user) use ($searchTerm) {
                $q_user->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        // Log::info("PesertaController DEBUG: Total items BEFORE pagination and filters: {$query->count()}"); // Opsional, untuk debug

        $paginator = $query->paginate($perPage);

        // Log::info("PesertaController DEBUG: Pagination results - Total: {$paginator->total()}, Last Page: {$paginator->lastPage()}, Next Page URL: " . ($paginator->nextPageUrl() ?? 'NULL')); // Opsional, untuk debug

        // Penting: Pastikan respons JSON sesuai format yang frontend harapkan (root properties)
        return response()->json($paginator);
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
            'pendidikan_id' => ['nullable', 'integer', 'exists:pendidikan,id'],
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
        $peserta = Peserta::with('user')->find($id); // Ambil peserta dengan user-nya

        if (!$peserta) {
            return response()->json(['message' => 'Data Peserta tidak ditemukan'], 404);
        }

        $user = $peserta->user; // Dapatkan objek User terkait

        if (!$user) {
            return response()->json(['message' => 'User terkait peserta tidak ditemukan.'], 404);
        }

        // Validasi data yang masuk
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255', // Nama user
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // Email user
            'nik_peserta' => ['sometimes', 'nullable', 'string', 'digits:16', Rule::unique('peserta', 'nik_peserta')->ignore($peserta->id)],
            'jenis_kelamin' => ['sometimes', 'nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'alamat_peserta' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'nomor_telp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'tanggal_lahir' => ['sometimes', 'nullable', 'date'],
            'foto_peserta' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'pendidikan_id' => ['sometimes', 'nullable', 'integer', 'exists:pendidikan,id'],
            'status_kerja' => ['sometimes', 'nullable', Rule::in(['bekerja', 'belum_bekerja', 'kuliah', 'wirausaha', 'tidak_diketahui'])],
            'pendidikan_id' => ['sometimes', 'nullable', 'integer', 'exists:pendidikan,id'], // Perbaikan nama field dari peserta_id
            'remove_foto_peserta' => ['sometimes', 'boolean'], // Tambahkan validasi untuk flag hapus foto
        ]);

        try {
            DB::transaction(function () use ($request, $user, $peserta, $validatedData) {
                    // --- LOGIKA VERIFIKASI EMAIL (SUDAH BENAR) ---
                if (isset($validatedData['email']) && $validatedData['email'] !== $user->email) {
                    $validatedData['email_verified_at'] = null; // Tambahkan ke data yang akan diupdate
                }
                // ---------------------------------------------

                // Update data User dengan data yang sudah divalidasi
                $userDataToUpdate = Arr::only($validatedData, ['name', 'email', 'email_verified_at']);
                if (!empty($userDataToUpdate)) {
                    $user->update($userDataToUpdate);
                }

                // Update data Peserta dengan data yang sudah divalidasi
                $pesertaDataToUpdate = Arr::only($validatedData, [
                    'nik_peserta', 'jenis_kelamin', 'alamat_peserta', 'nomor_telp',
                    'tanggal_lahir', 'status_kerja', 'pendidikan_id'
                ]);

                // Handle upload file untuk foto_peserta
                if ($request->hasFile('foto_peserta')) {
                    if ($peserta->foto_peserta && Storage::disk('public')->exists($peserta->foto_peserta)) {
                        Storage::disk('public')->delete($peserta->foto_peserta);
                    }
                    $path = $request->file('foto_peserta')->store('foto_profil_peserta', 'public');
                    $pesertaDataToUpdate['foto_peserta'] = $path;
                } 
                elseif ($request->input('remove_foto_peserta') && $peserta->foto_peserta) {
                    // Hapus foto jika ada flag 'remove_foto_peserta' dan foto memang ada
                    if (Storage::disk('public')->exists($peserta->foto_peserta)) {
                        Storage::disk('public')->delete($peserta->foto_peserta);
                    }
                    $pesertaDataToUpdate['foto_peserta'] = null; // Set field di DB menjadi null
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

        if (!$peserta) {
            return response()->json(['message' => 'Data Peserta tidak ditemukan'], 404);
        }

        $user = $peserta->user;

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
                $query->whereNotNull('tempat_kerja')->where('status', 'Ditampilkan');
            })
            
            ->whereNotNull('foto_peserta')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return PesertaPublicResource::collection($pesertaHighlights);
    }
}
