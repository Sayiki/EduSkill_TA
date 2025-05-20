<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileYayasan;

class ProfileYayasanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $items = ProfileYayasan::paginate($perPage);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_yayasan'      => 'required|string|max:255',
            'deskripsi_yayasan' => 'required|string',
            'foto_yayasan'      => 'nullable|string',
        ]);

        // rekam siapa admin yang buat
        $data['admin_id'] = $request->user()->id;

        $item = ProfileYayasan::create($data);

        return response()->json([
            'message' => 'Profile Yayasan berhasil dibuat',
            'data'    => $item,
        ], 201);
    }

    public function show($id)
    {
        $item = ProfileYayasan::findOrFail($id);

        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = ProfileYayasan::findOrFail($id);

        $data = $request->validate([
            'nama_yayasan'      => 'required|string|max:255',
            'deskripsi_yayasan' => 'required|string',
            'foto_yayasan'      => 'nullable|string',
        ]);

        // rekam siapa admin yang ubah
        $data['admin_id'] = $request->user()->id;

        $item->update($data);

        return response()->json([
            'message' => 'Profile Yayasan berhasil diperbarui',
            'data'    => $item,
        ]);
    }

    public function destroy($id)
    {
        ProfileYayasan::findOrFail($id)->delete();

        return response()->json(['message' => 'Profile Yayasan berhasil dihapus']);
    }
}
