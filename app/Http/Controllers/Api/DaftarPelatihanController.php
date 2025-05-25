<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaftarPelatihan;
use App\Models\Peserta;

class DaftarPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $entries = DaftarPelatihan::with(['peserta','pelatihan'])
                      ->paginate($perPage);

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        Log::info('Registration attempt by User ID: ' . $request->user()->id);

        // 1. VALIDATE INCOMING DATA
        $data = $request->validate([
            'id_pelatihan' => 'required|integer|exists:pelatihan,id',
            'nik'          => ['required', 'string', 'digits:16'],
            'kk'           => 'nullable|string|max:255',
            'ktp'          => 'nullable|string|max:255',
            'ijazah'       => 'nullable|string|max:255',
            'foto'         => 'nullable|string|max:255',
        ]);

        // 2. FIND OR CREATE THE PESERTA RECORD
        $peserta = Peserta::firstOrCreate(['user_id' => $request->user()->id]);

        // 3. HANDLE THE NIK
        if (empty($peserta->nik_peserta)) {
            $isNikTaken = Peserta::where('nik_peserta', $data['nik'])->where('id', '!=', $peserta->id)->exists();
            if ($isNikTaken) {
                return response()->json(['message' => 'NIK sudah terdaftar oleh peserta lain.'], 422);
            }
            $peserta->nik_peserta = $data['nik'];
            $peserta->save();
        } 
        elseif ($peserta->nik_peserta !== $data['nik']) {
            return response()->json([
                'message' => 'NIK yang Anda masukkan tidak sesuai dengan data yang sudah terdaftar.',
            ], 422);
        }
        
        // 4. PREVENT MULTIPLE ACTIVE REGISTRATIONS
        // UPDATED: Check for 'ditinjau' instead of 'menunggu'
        $hasActiveRegistration = DaftarPelatihan::where('id_peserta', $peserta->id)
                                                ->whereIn('status', ['ditinjau', 'diterima'])
                                                ->exists();

        if ($hasActiveRegistration) {
            return response()->json([
                'message' => 'Anda sudah memiliki pendaftaran yang sedang ditinjau atau sudah diterima. Anda tidak dapat mendaftar lagi saat ini.'
            ], 409); 
        }

        // ... (Your other checks can go here if needed)

        // 5. CREATE THE REGISTRATION ENTRY
        $entry = DaftarPelatihan::create([
            'id_peserta'   => $peserta->id,
            'id_pelatihan' => $data['id_pelatihan'],
            'kk'           => $data['kk'] ?? null,
            'ktp'          => $data['ktp'] ?? null,
            'ijazah'       => $data['ijazah'] ?? null,
            'foto'         => $data['foto'] ?? null,
            'status'       => 'ditinjau', // CHANGED AS REQUESTED
        ]);

        // 6. RETURN A SUCCESSFUL RESPONSE
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
            'peserta_id'        => 'sometimes|required|integer|exists:peserta,id',
            'pelatihan_id'      => 'sometimes|required|integer|exists:pelatihan,id',
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
