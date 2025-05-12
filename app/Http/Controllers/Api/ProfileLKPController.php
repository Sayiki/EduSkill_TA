<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileLKP;

class ProfileLKPController extends Controller
{
    public function index() {
        return response()->json(ProfileLKP::all());
    }

    // POST /api/profile-lkp
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_lembaga'     => 'required|exists:informasi_lembaga,id_lembaga',
            'nama_lkp'       => 'required|string|max:255',
            'deskripsi_lkp'  => 'required|string',
            'foto_lkp'       => 'nullable|string',
        ]);

        $lkp = ProfileLKP::create($data);

        return response()->json([
            'message' => 'Profile LKP berhasil dibuat',
            'data'    => $lkp,
        ], 201);
    }

    // GET /api/profile-lkp/{id}
    public function show($id)
    {
        $lkp = ProfileLKP::findOrFail($id);

        return response()->json(['data' => $lkp]);
    }

    // PUT /api/profile-lkp/{id}
    public function update(Request $request, $id)
    {
        $lkp = ProfileLKP::findOrFail($id);

        $data = $request->validate([
            'id_lembaga'     => 'required|exists:informasi_lembaga,id_lembaga',
            'nama_lkp'       => 'required|string|max:255',
            'deskripsi_lkp'  => 'required|string',
            'foto_lkp'       => 'nullable|string',
        ]);

        $lkp->update($data);

        return response()->json([
            'message' => 'Profile LKP berhasil diperbarui',
            'data'    => $lkp,
        ]);
    }

    // DELETE /api/profile-lkp/{id}
    public function destroy($id)
    {
        ProfileLKP::findOrFail($id)->delete();

        return response()->json(['message' => 'Profile LKP berhasil dihapus']);
    }
}
