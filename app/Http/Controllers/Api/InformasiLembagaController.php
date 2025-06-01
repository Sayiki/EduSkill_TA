<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiLembaga;
use App\Models\Admin; // Pastikan di-import
use Illuminate\Http\Request;
use App\Http\Resources\InformasiLembagaResource; // Import resource

class InformasiLembagaController extends Controller
{
    /**
     * Menampilkan informasi lembaga utama (publik).
     * Kita asumsikan hanya ada satu entri.
     */
    public function index(Request $request)
    {
        // Mengambil entri informasi lembaga pertama yang ada, atau null jika tidak ada
        $lembaga = InformasiLembaga::latest()->first(); 

        if (!$lembaga) {
            return response()->json(['data' => null, 'message' => 'Informasi lembaga belum diatur.'], 200); 
        }
        return new InformasiLembagaResource($lembaga);
    }

    /**
     * Menyimpan atau memperbarui informasi lembaga (hanya Admin).
     * Ini akan bertindak sebagai "upsert".
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'visi' => 'required|string',
            'misi' => 'required|string',
        ]);

        $loggedInUser = $request->user();
        // Pastikan user yang login adalah admin dan memiliki profil admin
        if (!$loggedInUser || !$loggedInUser->adminProfile) {
             return response()->json(['message' => 'Akses ditolak atau profil admin tidak ditemukan.'], 403);
        }
        $admin = $loggedInUser->adminProfile;
        
        // Coba cari apakah sudah ada entri informasi lembaga
        $lembaga = InformasiLembaga::first();

        if ($lembaga) {
            // Jika sudah ada, UPDATE entri yang ada
            $lembaga->update($validatedData);
            // Opsional: update admin_id jika ingin melacak editor terakhir
            // $lembaga->admin_id = $admin->id; 
            // $lembaga->save();
        } else {
            // Jika belum ada, CREATE entri baru
            $validatedData['admin_id'] = $admin->id; // Hanya set admin_id saat pembuatan awal
            $lembaga = InformasiLembaga::create($validatedData);
        }

        // Muat relasi jika Anda ingin menampilkan info admin di resource
        // $lembaga->load('admin.user'); 
        return new InformasiLembagaResource($lembaga->fresh());
    }

    /**
     * Menampilkan detail informasi lembaga spesifik (publik).
     * Endpoint ini mungkin tidak terlalu berguna jika hanya ada satu entri,
     * karena index() sudah mengembalikan entri tunggal tersebut.
     * Tapi kita biarkan untuk konsistensi jika diperlukan.
     */
    public function show($id)
    {
        // Jika kita selalu ingin mengambil entri tunggal, $id bisa diabaikan
        // $lembaga = InformasiLembaga::latest()->first();
        // Atau jika tetap ingin berdasarkan ID (misalnya ada kasus khusus):
        $lembaga = InformasiLembaga::find($id);

        if (!$lembaga) {
            return response()->json(['message' => 'Informasi lembaga tidak ditemukan'], 404);
        }
        return new InformasiLembagaResource($lembaga);
    }

    /**
     * Memperbarui informasi lembaga yang ada (hanya Admin).
     * Dengan pendekatan "upsert" di store, method update ini menjadi redundant
     * kecuali jika Anda ingin endpoint PUT terpisah yang hanya bisa mengupdate.
     * Disarankan untuk menggunakan endpoint store (POST) saja untuk kesederhanaan.
     */
    public function update(Request $request, $id)
    {
        // Ambil satu-satunya record lembaga, atau record dengan ID spesifik
        $lembaga = InformasiLembaga::findOrFail($id); // Atau firstOrFail() jika $id diabaikan

        $validatedData = $request->validate([
            'visi' => 'sometimes|required|string',
            'misi' => 'sometimes|required|string',
        ]);

        // admin_id (pembuat asli) tidak diubah saat update.
        $lembaga->update($validatedData);

        return new InformasiLembagaResource($lembaga->fresh());
    }

    /**
     * Menghapus informasi lembaga (hanya Admin).
     * Endpoint ini sebaiknya TIDAK ADA jika informasi lembaga harus selalu ada.
     * Jika dihapus, method store() akan membuat yang baru lagi.
     */
    public function destroy($id)
    {
        $lembaga = InformasiLembaga::findOrFail($id);
        
        // Otorisasi tambahan jika perlu
        
        $lembaga->delete();

        return response()->json(['message' => 'Informasi lembaga berhasil dihapus'], 200);
    }
}
