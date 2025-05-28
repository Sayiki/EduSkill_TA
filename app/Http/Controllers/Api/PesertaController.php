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

class PesertaController extends Controller
{
    // ✅ GET /api/peserta
    public function index(Request $request)   // ← inject the Request
    {
        // allow client to pass ?per_page=… (default to 15)
        $perPage = $request->query('per_page', 10);

        $paginator = Peserta::with(['user', 'pendidikan'])
            ->paginate($perPage);

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
            'status_kerja' => ['nullable', Rule::in(['bekerja', 'belum_bekerja', 'kuliah', 'wirausaha', 'tidak_diketahui'])],
            'id_pendidikan' => ['nullable', 'integer', 'exists:pendidikan,id'],
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
        // Selalu load relasi 'user'
        $peserta = Peserta::with(['user', 'pendidikan'])->find($id);

        if (!$peserta) {
            return response()->json(['message' => 'Data Peserta tidak ditemukan'], 404);
        }

        // Otorisasi tambahan jika diperlukan:
        // Jika yang mengakses adalah peserta, pastikan dia hanya mengakses profilnya sendiri.
        $loggedInUser = auth()->user();
        if ($loggedInUser->peran === 'peserta' && $peserta->user_id !== $loggedInUser->id) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melihat profil ini.'], 403);
        }

        return response()->json(['data' => $peserta]);
    }

    public function update(Request $request, $id)
    {
        $peserta = Peserta::find($id);

        if (!$peserta) {
            return response()->json(['message' => 'Data Peserta tidak ditemukan'], 404);
        }

        $userToUpdate = $peserta->user; // Dapatkan model User yang berelasi

        // Otorisasi: Peserta hanya boleh mengedit profilnya sendiri. Admin boleh mengedit siapa saja.
        $loggedInUser = $request->user();
        if ($loggedInUser->peran === 'peserta' && $peserta->user_id !== $loggedInUser->id) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk memperbarui profil ini.'], 403);
        }

        // Validasi data. Gunakan 'sometimes' agar hanya field yang dikirim yang divalidasi.
        $validatedData = $request->validate([
            // Fields untuk tabel 'users'
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userToUpdate->id)],
            // Anda bisa menambahkan validasi untuk 'username' jika diperlukan
            // 'username' => ['sometimes', 'string', 'max:255', Rule::unique('users')->ignore($userToUpdate->id)],

            // Fields untuk tabel 'peserta'
            'nik_peserta' => ['sometimes', 'nullable', 'string', 'digits:16', Rule::unique('peserta', 'nik_peserta')->ignore($peserta->id)],
            'jenis_kelamin' => ['sometimes', 'nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'alamat_peserta' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'nomor_telp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'tanggal_lahir' => ['sometimes', 'nullable', 'date'],
            'foto_peserta' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // Max 2MB
            'status_kerja' => ['sometimes', 'nullable', Rule::in(['bekerja', 'belum_bekerja', 'kuliah', 'wirausaha', 'tidak_diketahui'])],
            'id_pendidikan' => ['sometimes', 'nullable', 'integer', 'exists:pendidikan,id'],
        ]);

        try {
            DB::transaction(function () use ($request, $userToUpdate, $peserta, $validatedData) {
                // 1. Update data di tabel 'users'
                $userDataToUpdate = [];
                if ($request->has('name')) $userDataToUpdate['name'] = $validatedData['name'];
                if ($request->has('email')) $userDataToUpdate['email'] = $validatedData['email'];
                // if ($request->has('username')) $userDataToUpdate['username'] = $validatedData['username'];

                if (!empty($userDataToUpdate)) {
                    $userToUpdate->update($userDataToUpdate);
                }

                // 2. Update data di tabel 'peserta'
                $pesertaDataToUpdate = [];
                if ($request->has('nik_peserta')) $pesertaDataToUpdate['nik_peserta'] = $validatedData['nik_peserta'];
                if ($request->has('jenis_kelamin')) $pesertaDataToUpdate['jenis_kelamin'] = $validatedData['jenis_kelamin'];
                if ($request->has('alamat_peserta')) $pesertaDataToUpdate['alamat_peserta'] = $validatedData['alamat_peserta'];
                if ($request->has('nomor_telp')) $pesertaDataToUpdate['nomor_telp'] = $validatedData['nomor_telp'];
                if ($request->has('tanggal_lahir')) $pesertaDataToUpdate['tanggal_lahir'] = $validatedData['tanggal_lahir'];
                if ($request->has('status_kerja')) $pesertaDataToUpdate['status_kerja'] = $validatedData['status_kerja'];
                if ($request->has('id_pendidikan')) $pesertaDataToUpdate['id_pendidikan'] = $validatedData['id_pendidikan'];


                // Handle file upload untuk foto_peserta
                if ($request->hasFile('foto_peserta')) {
                    // Hapus foto lama jika ada
                    if ($peserta->foto_peserta && Storage::disk('public')->exists($peserta->foto_peserta)) {
                        Storage::disk('public')->delete($peserta->foto_peserta);
                    }
                    $path = $request->file('foto_peserta')->store('foto_profil_peserta', 'public');
                    $pesertaDataToUpdate['foto_peserta'] = $path;
                } elseif ($request->filled('remove_foto_peserta') && $request->boolean('remove_foto_peserta')) {
                     // Logika jika ingin menghapus foto tanpa mengupload yang baru
                    if ($peserta->foto_peserta && Storage::disk('public')->exists($peserta->foto_peserta)) {
                        Storage::disk('public')->delete($peserta->foto_peserta);
                    }
                    $pesertaDataToUpdate['foto_peserta'] = null;
                }


                if (!empty($pesertaDataToUpdate)) {
                    $peserta->update($pesertaDataToUpdate);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui profil.', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $peserta->fresh()->load(['user', 'pendidikan']), // Kirim data peserta yang sudah fresh
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
}
