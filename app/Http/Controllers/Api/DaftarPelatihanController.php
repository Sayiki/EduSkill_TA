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
        // 1. Temukan entri pendaftaran atau gagal jika tidak ada.
        $entry = DaftarPelatihan::with('pelatihan')->findOrFail($id);

        // 2. Validasi input yang masuk. Fokus utama pada perubahan status.
        $data = $request->validate([
            'status' => ['required', 'in:ditinjau,diterima,ditolak'],
            // Kita bisa juga memvalidasi field dokumen jika diperlukan,
            // tapi untuk sekarang kita fokus pada status.
        ]);

        $newStatus = $data['status'];
        $originalStatus = $entry->status;

        // Jangan lakukan apa-apa jika status tidak berubah.
        if ($newStatus === $originalStatus) {
            return response()->json($entry->load('peserta'), 200);
        }

        // 3. LOGIKA BISNIS: Periksa kuota SEBELUM menerima peserta baru.
        if ($newStatus === 'diterima') {
            $pelatihan = $entry->pelatihan;
            if ($pelatihan->jumlah_peserta >= $pelatihan->jumlah_kuota) {
                // Jika kuota sudah penuh, kembalikan error.
                return response()->json([
                    'message' => 'Gagal menerima peserta. Kuota untuk pelatihan ini sudah penuh.',
                    'kuota' => $pelatihan->jumlah_kuota,
                    'peserta_saat_ini' => $pelatihan->jumlah_peserta,
                ], 409); // 409 Conflict
            }
        }

        // 4. Lakukan update pada entri pendaftaran.
        $entry->update($data);

        // 5. LOGIKA BISNIS: Update jumlah peserta pada pelatihan terkait.
        // Jika status baru adalah 'diterima' (dan status lama bukan 'diterima').
        if ($newStatus === 'diterima' && $originalStatus !== 'diterima') {
            $entry->pelatihan->increment('jumlah_peserta');
        } 
        // Jika status lama adalah 'diterima' dan diubah ke status lain (ditolak/ditinjau).
        elseif ($originalStatus === 'diterima' && $newStatus !== 'diterima') {
            $entry->pelatihan->decrement('jumlah_peserta');
        }

        // 6. Kembalikan respons dengan data yang sudah diperbarui.
        // Muat ulang relasi untuk memastikan data yang dikirim adalah yang terbaru.
        return response()->json($entry->fresh()->load(['peserta', 'pelatihan']), 200);
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
