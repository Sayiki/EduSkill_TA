<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;

class PesertaController extends Controller
{
    // ✅ GET /api/peserta
    public function index(Request $request)   // ← inject the Request
    {
        // allow client to pass ?per_page=… (default to 15)
        $perPage = $request->query('per_page', 10);

        $paginator = Peserta::with(['user', 'pendidikan'])
            ->paginate($perPage);

        return response()->json($paginator);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => ['required', 'integer', 'exists:users,id'],
            'nik_peserta'   => ['required', 'string', 'max:100', 'unique:peserta,nik_peserta'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki','Perempuan'])],
            'alamat_peserta'=> ['required', 'string', 'max:1000'],
            'id_pendidikan' => ['nullable', 'integer', 'exists:pendidikan,id'],
        ]);

        $peserta = Peserta::create($data);

        return response()->json([
            'message' => 'Peserta berhasil dibuat',
            'data'    => $peserta->load(['user','pendidikan']),
        ], 201);
    }

    public function show($id)
    {
        $peserta = Peserta::with(['user','pendidikan'])->find($id);

        if (! $peserta) {
            return response()->json(['error' => 'Peserta tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $peserta,
        ]);
    }

    public function update(Request $request, $id)
    {
        $peserta = Peserta::find($id);

        if (! $peserta) {
            return response()->json(['error' => 'Peserta tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'user_id'       => ['sometimes','required','integer','exists:users,id'],
            'nik_peserta'   => ['sometimes','required','string','max:100', Rule::unique('peserta','nik_peserta')->ignore($peserta->id)],
            'jenis_kelamin' => ['sometimes','required', Rule::in(['Laki-laki','Perempuan'])],
            'alamat_peserta'=> ['sometimes','required','string','max:1000'],
            'id_pendidikan' => ['nullable','integer','exists:pendidikan,id'],
        ]);

        $peserta->update($data);

        return response()->json([
            'message' => 'Peserta berhasil diperbarui',
            'data'    => $peserta->load(['user','pendidikan']),
        ]);
    }

    public function destroy($id)
    {
        $peserta = Peserta::find($id);

        if (! $peserta) {
            return response()->json(['error' => 'Peserta tidak ditemukan'], 404);
        }

        $peserta->delete();

        return response()->json([
            'message' => 'Peserta berhasil dihapus',
        ]);
    }
}
