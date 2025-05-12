<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaftarPelatihan;

class DaftarPelatihanController extends Controller
{
    public function index() {
        return response()->json(DaftarPelatihan::with(['peserta','pelatihan'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_peserta'        => 'required|integer|exists:pesertas,id',
            'id_pelatihan'      => 'required|integer|exists:pelatihans,id',
            'kk'                => 'nullable|string',
            'ktp'               => 'nullable|string',
            'ijazah'            => 'nullable|string',
            'foto'              => 'nullable|string',
            'status'=> 'required|in:menunggu,diterima,ditolak,'
        ]);

        $entry = DaftarPelatihan::create($data);

        return response()->json($entry->load(['peserta', 'pelatihan']), 201);
    }

    /**
     * GET /api/daftar-pelatihan/{id}
     */
    public function show($id)
    {
        $entry = DaftarPelatihan::with(['peserta', 'pelatihan'])->findOrFail($id);
        return response()->json($entry, 200);
    }

    /**
     * PUT /api/daftar-pelatihan/{id}
     */
    public function update(Request $request, $id)
    {
        $entry = DaftarPelatihan::findOrFail($id);

        $data = $request->validate([
            'peserta_id'        => 'sometimes|required|integer|exists:pesertas,id',
            'pelatihan_id'      => 'sometimes|required|integer|exists:pelatihans,id',
            'kk'                => 'nullable|string',
            'ktp'               => 'nullable|string',
            'ijazah'            => 'nullable|string',
            'foto'              => 'nullable|string',
            'status'=> 'sometimes|required|in:menunggu,diterima,ditolak',
        ]);

        $entry->update($data);

        return response()->json($entry->load(['peserta', 'pelatihan']), 200);
    }

    /**
     * DELETE /api/daftar-pelatihan/{id}
     */
    public function destroy($id)
    {
        $entry = DaftarPelatihan::findOrFail($id);
        $entry->delete(); 

        return response()->json(null, 204);
    }
}
