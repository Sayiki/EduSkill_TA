<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileYayasan;

class ProfileYayasanController extends Controller
{
    public function index() {
        return response()->json(ProfileYayasan::all());
    }

    // POST /api/profile-yayasan
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_yayasan'      => 'required|string|max:255',
            'deskripsi_yayasan' => 'required|string',
            'foto_yayasan'      => 'nullable|string',
        ]);

        $yayasan = ProfileYayasan::create($data);

        return response()->json([
            'message' => 'Profile Yayasan berhasil dibuat',
            'data'    => $yayasan,
        ], 201);
    }

    // GET /api/profile-yayasan/{id}
    public function show($id)
    {
        $yayasan = ProfileYayasan::findOrFail($id);

        return response()->json(['data' => $yayasan]);
    }

    // PUT /api/profile-yayasan/{id}
    public function update(Request $request, $id)
    {
        $yayasan = ProfileYayasan::findOrFail($id);

        $data = $request->validate([
            'nama_yayasan'      => 'required|string|max:255',
            'deskripsi_yayasan' => 'required|string',
            'foto_yayasan'      => 'nullable|string',
        ]);

        $yayasan->update($data);

        return response()->json([
            'message' => 'Profile Yayasan berhasil diperbarui',
            'data'    => $yayasan,
        ]);
    }

    // DELETE /api/profile-yayasan/{id}
    public function destroy($id)
    {
        ProfileYayasan::findOrFail($id)->delete();

        return response()->json(['message' => 'Profile Yayasan berhasil dihapus']);
    }
}
