<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileLPK;

class ProfileLPKController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $items = ProfileLPK::paginate($perPage);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_lembaga'    => 'required|exists:informasi_lembaga,id',
            'nama_lpk'      => 'required|string|max:255',
            'deskripsi_lpk' => 'required|string',
            'foto_lpk'      => 'nullable|string',
        ]);

        $item = ProfileLPK::create($data);

        return response()->json([
            'message' => 'Profile LPK berhasil dibuat',
            'data'    => $item,
        ], 201);
    }

    public function show($id)
    {
        $item = ProfileLPK::findOrFail($id);

        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = ProfileLPK::findOrFail($id);

        $data = $request->validate([
            'id_lembaga'    => 'required|exists:informasi_lembaga,id',
            'nama_lpk'      => 'required|string|max:255',
            'deskripsi_lpk' => 'required|string',
            'foto_lpk'      => 'nullable|string',
        ]);

        $item->update($data);

        return response()->json([
            'message' => 'Profile LPK berhasil diperbarui',
            'data'    => $item,
        ]);
    }

    public function destroy($id)
    {
        ProfileLPK::findOrFail($id)->delete();

        return response()->json(['message' => 'Profile LPK berhasil dihapus']);
    }
}
