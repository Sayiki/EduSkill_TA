<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PesertaAkunController extends Controller
{
    public function updateProfile(Request $request, $id)
    {
        $akun = PesertaAkun::where('id_peserta', $id)->first();

        if (!$akun) {
            return response()->json(['error' => 'Akun tidak ditemukan'], 404);
        }

        // Update both peserta_akun and peserta
        $akun->update([
            'nama_peserta' => $request->nama_peserta,
            'alamat_peserta' => $request->alamat_peserta,
            'jenis_kelamin' => $request->jenis_kelamin,
            'pendidikan_peserta' => $request->pendidikan_peserta,
        ]);

        $akun->peserta->update([
            'nama_peserta' => $request->nama_peserta, // Sync with peserta table
        ]);

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
