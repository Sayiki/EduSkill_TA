<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Peserta;
use App\Models\User;
use App\Models\DaftarPelatihan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        // Tambahkan filter status dan search jika diperlukan
        $statusFilter = $request->query('status'); // Asumsi frontend akan mengirim parameter status
        $searchQuery = $request->query('search'); // Asumsi frontend akan mengirim parameter search

        $query = Feedback::with([
            'peserta.user',
            'daftarPelatihan.pelatihan'
        ]);

        if ($statusFilter && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($searchQuery) {
            $query->whereHas('peserta.user', function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%');
            })->orWhere('comment', 'like', '%' . $searchQuery . '%'); // Tambahkan pencarian berdasarkan comment
        }


        $fb = $query->paginate($perPage);

        return response()->json($fb);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        if (!$peserta) {
            return response()->json(['message' => 'Profil peserta tidak ditemukan untuk pengguna yang sedang login.'], 404);
        }

        $validatedData = $request->validate([
            'daftar_pelatihan_id' => 'required|integer|exists:daftar_pelatihan,id',
            'comment'             => 'required|string|max:1000',
            'tempat_kerja'        => ['nullable', 'string', 'max:255'],
            'status'              => ['sometimes', 'string', Rule::in(['Ditinjau', 'Ditampilkan', 'Tidak Ditampilkan'])], // BARU: Validasi status
        ]);

        $validatedData['peserta_id'] = $peserta->id;

        $daftarPelatihan = DaftarPelatihan::where('id', $validatedData['daftar_pelatihan_id'])
                                          ->where('peserta_id', $peserta->id)
                                          ->first();

        if (!$daftarPelatihan) {
            return response()->json(['message' => 'Pendaftaran pelatihan tidak valid atau bukan milik pengguna ini.'], 403);
        }

        // Set nilai default untuk status jika tidak disediakan dari request
        if (!isset($validatedData['status'])) {
            $validatedData['status'] = 'Ditinjau';
        }

        $feedback = Feedback::create($validatedData);

        return response()->json(
            $feedback->load([
                'peserta.user',
                'daftarPelatihan.pelatihan'
            ]),
            201
        );
    }

    /**
     * GET /api/feedback/{id}
     */
    public function show($id)
    {
        $fb = Feedback::with([
                        'peserta.user',
                        'daftarPelatihan.pelatihan'
                    ])->findOrFail($id);
        return response()->json($fb, 200);
    }

    /**
     * PUT /api/feedback/{id}
     */
    public function update(Request $request, $id)
    {
        $fb = Feedback::findOrFail($id);

        $data = $request->validate([
            'comment'             => 'sometimes|required|string|max:1000',
            'tempat_kerja'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'daftar_pelatihan_id' => 'sometimes|required|integer|exists:daftar_pelatihan,id',
            'status'              => ['sometimes', 'required', 'string', Rule::in(['Ditinjau', 'Ditampilkan', 'Tidak Ditampilkan'])], // BARU: Validasi status
        ]);

        $fb->update($data);

        return response()->json(
            $fb->load([
                'peserta.user',
                'daftarPelatihan.pelatihan'
            ]),
            200
        );
    }

    /**
     * DELETE /api/feedback/{id}
     */
    public function destroy($id)
    {
        $fb = Feedback::findOrFail($id);

        $fb->delete();

        return response()->json(['message' => 'Feedback berhasil dihapus.'], 200);
    }
}