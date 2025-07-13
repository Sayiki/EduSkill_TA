<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ketua;
use Illuminate\Support\Facades\DB;
use App\Models\DaftarPelatihan;
use App\Models\Pelatihan;
use App\Models\Peserta;        
use App\Models\Feedback;       
use App\Models\LaporanAdmin;  

class KetuaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $ketua = Ketua::with('user')
                      ->paginate($perPage);

        return response()->json($ketua);
    }

    /**
     * POST /api/ketua
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $ketua = Ketua::create($payload);

        return response()->json([
            'message' => 'Ketua berhasil dibuat.',
            'data'    => $ketua->load('user'),
        ], 201);
    }

    /**
     * GET /api/ketua/{id}
     */
    public function show($id)
    {
        $ketua = Ketua::with('user')->findOrFail($id);

        return response()->json(['data' => $ketua]);
    }

    /**
     * PUT /api/ketua/{id}
     */
    public function update(Request $request, $id)
    {
        $ketua = Ketua::findOrFail($id);

        $payload = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $ketua->update($payload);

        return response()->json([
            'message' => 'Ketua berhasil diperbarui.',
            'data'    => $ketua->load('user'),
        ]);
    }

    /**
     * DELETE /api/ketua/{id}
     */
    public function destroy($id)
    {
        $ketua = Ketua::findOrFail($id);
        $ketua->delete();

        return response()->json([
            'message' => 'Ketua berhasil dihapus.',
        ]);
    }

    public function getDashboardData(Request $request)
    {
        // 1. Hitung Statistik berdasarkan definisi Anda
        $jumlahPendaftar = DB::table('daftar_pelatihan')->count();
        $jumlahPeserta = DB::table('daftar_pelatihan')->where('status', 'diterima')->count();
        // Asumsi status lulus adalah 'lulus'. Sesuaikan jika berbeda.
        $jumlahAlumni = DB::table('peserta')->where('status_lulus', 'lulus')->count();
        $totalPelatihan = Pelatihan::count();

        // 2. Ambil data untuk tabel (misalnya, 5 data terbaru)
        $pelatihanData = Pelatihan::with('kategori')->latest()->take(5)->get();
        $laporanAdminData = LaporanAdmin::with('admin.user:id,name')->latest()->take(5)->get();
        $tempatKerjaData = Feedback::with('peserta.user:id,name')
                                    ->whereNotNull('tempat_kerja')
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // 3. Kembalikan semua data dalam satu response JSON
        return response()->json([
            'stats' => [
                'jumlahPendaftar' => $jumlahPendaftar,
                'jumlahPeserta' => $jumlahPeserta,
                'totalPelatihan' => $totalPelatihan,
                'jumlahAlumni' => $jumlahAlumni,
            ],
            'tables' => [
                'pelatihan' => $pelatihanData,
                'laporanAdmin' => $laporanAdminData,
                'tempatKerja' => $tempatKerjaData,
            ]
        ]);
    }
}
