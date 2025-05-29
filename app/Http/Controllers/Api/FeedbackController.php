<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $fb = Feedback::with('peserta')
                    ->paginate($perPage);

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
            'comment'      => 'required|string|max:1000', 
            'status_kerja' => ['nullable', 'string', 'max:20', Rule::in(['bekerja', 'belum_bekerja', 'kuliah', 'wirausaha', 'tidak_diketahui'])],
        ]);
        

        $validatedData['id_peserta'] = $peserta->id;


        $feedback = Feedback::create($validatedData);

        return response()->json(
            $feedback->load(['peserta' => function ($query) {
                $query->with('user');
            }]),
            201
        );
    }

    /**
     * GET /api/feedback/{id}
     */
    public function show($id)
    {
        $fb = Feedback::with('peserta')->findOrFail($id);
        return response()->json($fb, 200);
    }

    /**
     * PUT /api/feedback/{id}
     */
    public function update(Request $request, $id)
    {
        $fb = Feedback::findOrFail($id);

        // Otorisasi: Hanya admin yang boleh update, atau peserta pemilik feedback (jika diizinkan)
        // $this->authorize('update', $fb);

        $data = $request->validate([
            'comment'      => 'sometimes|required|string|max:1000',
            'status_kerja' => ['sometimes', 'nullable', 'string', 'max:20', Rule::in(['bekerja', 'belum_bekerja', 'kuliah', 'wirausaha', 'tidak_diketahui'])],
        ]);

        $fb->update($data);

        return response()->json(
            $fb->load(['peserta' => function ($query) {
                $query->with('user');
            }]),
            200
        );
    }

    /**
     * DELETE /api/feedback/{id}
     */
    public function destroy($id)
    {
        $fb = Feedback::findOrFail($id);

        // Otorisasi: Hanya admin yang boleh delete
        // $this->authorize('delete', $fb);

        $fb->delete(); 

        return response()->json(['message' => 'Feedback berhasil dihapus.'], 200);
    }
}
