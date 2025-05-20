<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileLKP;

class ProfileLKPController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $items = ProfileLKP::paginate($perPage);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_lembaga'    => 'required|exists:informasi_lembaga,id',
            'nama_lkp'      => 'required|string|max:255',
            'deskripsi_lkp' => 'required|string',
            'foto_lkp'      => 'nullable|string',
        ]);

        $item = ProfileLKP::create($data);

        return response()->json([
            'message' => 'Profile LKP berhasil dibuat',
            'data'    => $item,
        ], 201);
    }

    public function show($id)
    {
        $item = ProfileLKP::findOrFail($id);

        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = ProfileLKP::findOrFail($id);

        $data = $request->validate([
            'id_lembaga'    => 'required|exists:informasi_lembaga,id',
            'nama_lkp'      => 'required|string|max:255',
            'deskripsi_lkp' => 'required|string',
            'foto_lkp'      => 'nullable|string',
        ]);

        $item->update($data);

        return response()->json([
            'message' => 'Profile LKP berhasil diperbarui',
            'data'    => $item,
        ]);
    }

    public function destroy($id)
    {
        ProfileLKP::findOrFail($id)->delete();

        return response()->json(['message' => 'Profile LKP berhasil dihapus']);
    }
}
