<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiKontak;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\InformasiKontakResource;

class InformasiKontakController extends Controller
{
    /**
     * Menampilkan daftar informasi kontak (publik).
     * Biasanya hanya akan ada satu atau beberapa entri kontak utama.
     */
    public function index(Request $request)
    {
        // Karena biasanya hanya ada satu entri, kita bisa langsung ambil yang pertama
        // atau yang terakhir dibuat, tergantung kebutuhan.
        // Untuk ContactInfo, kita hanya butuh 1.
        $kontak = InformasiKontak::latest()->first(); // Ambil record terbaru (asumsi 1 record utama)

        if (!$kontak) {
            return response()->json(['message' => 'Informasi kontak belum tersedia'], 404);
        }
        // Gunakan resource untuk memformat single item
        return new InformasiKontakResource($kontak); // Mengembalikan single resource
    }

    /**
     * Menyimpan informasi kontak baru (hanya Admin).
     * Metode ini juga berfungsi sebagai update jika record sudah ada.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'alamat'    => 'required|string|max:1000',
            'email'     => ['required', 'email', 'max:255', Rule::unique('informasi_kontak')->ignore(InformasiKontak::first()->id ?? null)], // Unique rule for email
            'telepon'   => 'required|string|max:50',
            'whatsapp'  => 'nullable|string|max:50', // BARU: Tambah validasi
            'instagram' => 'nullable|string|max:255', // BARU: Tambah validasi
        ]);

        $loggedInUser = $request->user();
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Akses ditolak atau profil admin tidak ditemukan.'], 403);
        }
        $admin = $loggedInUser->adminProfile;

        // Coba cari apakah sudah ada entri kontak. Asumsi hanya ada SATU record utama.
        $kontak = InformasiKontak::first();

        if ($kontak) {
            // Jika sudah ada, UPDATE entri yang ada
            $kontak->update($validatedData);
        } else {
            // Jika belum ada, CREATE entri baru
            $validatedData['admin_id'] = $admin->id;
            $kontak = InformasiKontak::create($validatedData);
        }

        return new InformasiKontakResource($kontak->fresh());
    }

    /**
     * Menampilkan detail informasi kontak spesifik (publik).
     * (Tidak diubah, tapi tidak relevan untuk ContactInfo.jsx karena kita ambil yang pertama)
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
     * Disarankan untuk menggunakan `store` untuk logic upsert (create/update first)
     * jika Anda hanya memiliki satu record. Jika tidak, $id harus selalu spesifik.
     */
    public function update(Request $request, $id)
    {
        // Mengambil record dengan ID spesifik, atau yang pertama jika $id tidak digunakan (hati-hati)
        $kontak = InformasiKontak::findOrFail($id); // Gunakan $id yang masuk dari route


        $validatedData = $request->validate([
            'alamat'    => 'sometimes|required|string|max:1000',
            'email'     => ['sometimes', 'required', 'email', 'max:255', Rule::unique('informasi_kontak')->ignore($kontak->id)],
            'telepon'   => 'sometimes|required|string|max:50',
            'whatsapp'  => 'sometimes|nullable|string|max:50', // BARU: Tambah validasi
            'instagram' => 'sometimes|nullable|string|max:255', // BARU: Tambah validasi
        ]);

        $kontak->update($validatedData);

        return new InformasiKontakResource($kontak->fresh());
    }

    /**
     * Menghapus informasi kontak (hanya Admin).
     * (Tidak diubah, tapi hati-hati menghapus satu-satunya record)
     */
    public function destroy($id)
    {
        $kontak = InformasiKontak::findOrFail($id);
        $kontak->delete();
        return response()->json(['message' => 'Informasi kontak berhasil dihapus'], 200);
    }
}