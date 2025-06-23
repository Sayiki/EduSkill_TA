<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiKontak;
use App\Models\Admin; // Pastikan di-import
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Untuk validasi
use App\Http\Resources\InformasiKontakResource; // Import resource

class InformasiKontakController extends Controller
{
    /**
     * Menampilkan daftar informasi kontak (publik).
     * Biasanya hanya akan ada satu atau beberapa entri kontak utama.
     */
    public function index(Request $request)
    {
        // Jika Anda hanya punya satu set informasi kontak, gunakan first()
        // $kontak = InformasiKontak::latest()->first(); // Mengambil yang terbaru
        // return $kontak ? new InformasiKontakResource($kontak) : response()->json(['data' => null]);

        // Jika bisa ada beberapa, gunakan paginasi atau get()
        $kontak = InformasiKontak::latest()->get(); // Mengambil semua, diurutkan terbaru
        return InformasiKontakResource::collection($kontak);
    }

    /**
     * Menyimpan informasi kontak baru (hanya Admin).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'alamat'  => 'required|string|max:1000',
            'email'   => 'required|email|max:255',
            'telepon' => 'required|string|max:50',
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Akses ditolak atau profil admin tidak ditemukan.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        
        // Coba cari apakah sudah ada entri kontak
        $kontak = InformasiKontak::first();

        if ($kontak) {
            // Jika sudah ada, UPDATE entri yang ada
            $kontak->update($validatedData);
            // Anda mungkin ingin mencatat admin_id yang melakukan update jika berbeda
            // $kontak->admin_id = $admin->id; // Jika ingin melacak editor terakhir
            // $kontak->save();
        } else {
            // Jika belum ada, CREATE entri baru
            $validatedData['admin_id'] = $admin->id;
            $kontak = InformasiKontak::create($validatedData);
        }

        return new InformasiKontakResource($kontak->fresh()); // Gunakan fresh() untuk data terbaru
    }

    /**
     * Menampilkan detail informasi kontak spesifik (publik).
     * Endpoint ini mungkin tidak terlalu berguna jika Anda hanya punya satu set info kontak.
     * Lebih umum mengambil semua via index() dan frontend memilih yang ditampilkan.
     */
    public function show($id)
    {
        $kontak = InformasiKontak::find($id);
        if (!$kontak) {
            return response()->json(['message' => 'Informasi kontak tidak ditemukan'], 404);
        }
        return new InformasiKontakResource($kontak);
    }

    /**
     * Memperbarui informasi kontak yang ada (hanya Admin).
     */
    public function update(Request $request, $id) // $id di sini mungkin tidak relevan jika hanya ada 1 record
    {
        // Ambil satu-satunya record kontak, atau record dengan ID spesifik jika Anda mengizinkan >1
        $kontak = InformasiKontak::firstOrFail(); // Mengambil yang pertama, atau gagal jika tidak ada
        // Atau jika Anda tetap menggunakan $id:
        // $kontak = InformasiKontak::findOrFail($id);


        $validatedData = $request->validate([
            'alamat'  => 'sometimes|required|string|max:1000',
            'email'   => ['sometimes', 'required', 'email', 'max:255', Rule::unique('informasi_kontak')->ignore($kontak->id)],
            'telepon' => 'sometimes|required|string|max:50',
        ]);

        $kontak->update($validatedData);

        return new InformasiKontakResource($kontak->fresh());
    }

    /**
     * Menghapus informasi kontak (hanya Admin).
     * Hati-hati menggunakan ini jika Anda hanya ingin ada satu set info kontak.
     * Mungkin lebih baik hanya ada fitur update.
     */
    public function destroy($id)
    {
        $kontak = InformasiKontak::findOrFail($id);
        
        // Otorisasi tambahan jika perlu (misalnya, hanya super admin)
        
        $kontak->delete();

        return response()->json(['message' => 'Informasi kontak berhasil dihapus'], 200);
    }
}
