<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileLPK;

class ProfileLPKController extends Controller
{
    public function index() {
        return response()->json(ProfileLPK::all());
    }

    // POST /api/profile-lpk
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_lembaga'     => 'required|exists:informasi_lembaga,id_lembaga',
            'nama_lpk'       => 'required|string|max:255',
            'deskripsi_lpk'  => 'required|string',
            'foto_lpk'       => 'nullable|string',
        ]);

        $lpk = ProfileLPK::create($data);

        return response()->json([
            'message' => 'Profile LPK berhasil dibuat',
            'data'    => $lpk,
        ], 201);
    }

    // GET /api/profile-lpk/{id}
    public function show($id)
    {
        $lpk = ProfileLPK::findOrFail($id);

        return response()->json(['data' => $lpk]);
    }

    // PUT /api/profile-lpk/{id}
    public function update(Request $request, $id)
    {
        $lpk = ProfileLPK::findOrFail($id);

        $data = $request->validate([
            'id_lembaga'     => 'required|exists:informasi_lembaga,id_lembaga',
            'nama_lpk'       => 'required|string|max:255',
            'deskripsi_lpk'  => 'required|string',
            'foto_lpk'       => 'nullable|string',
        ]);

        $lpk->update($data);

        return response()->json([
            'message' => 'Profile LPK berhasil diperbarui',
            'data'    => $lpk,
        ]);
    }

    // DELETE /api/profile-lpk/{id}
    public function destroy($id)
    {
        ProfileLPK::findOrFail($id)->delete();

        return response()->json(['message' => 'Profile LPK berhasil dihapus']);
    }
}
