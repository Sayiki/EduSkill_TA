<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\DaftarPelatihan;
use App\Models\Pelatihan;
use App\Models\Peserta;        
use App\Models\Feedback;       
use App\Models\LaporanAdmin;  
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // you can pass ?per_page=20 in the querystring, default to 10
        $perPage = $request->query('per_page', 10);

        $admins = Admin::with('user')->paginate($perPage);

        return response()->json($admins);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:25',
            'email'    => 'required|email|unique:admin,email',
            'password' => 'required|string|min:6',
        ]);

        // Hash password sebelum simpan
        $data['password'] = Hash::make($data['password']);

        $admin = Admin::create($data);

        return response()->json([
            'message' => 'Admin berhasil dibuat',
            'data'    => $admin,
        ], 201);
    }

    public function show($id)
    {
        $admin = Admin::find($id);

        if (! $admin) {
            return response()->json(['error' => 'Admin tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => $admin,
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (! $admin) {
            return response()->json(['error' => 'Admin tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'nama'     => 'sometimes|required|string|max:25',
            'email'    => "sometimes|required|email|unique:admin,email,{$id}",
            'password' => 'sometimes|required|string|min:6',
        ]);

        // Jika ada password, hash
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $admin->update($data);

        return response()->json([
            'message' => 'Admin berhasil diperbarui',
            'data'    => $admin,
        ]);
    }

    public function destroy($id)
    {
        $admin = Admin::find($id);

        if (! $admin) {
            return response()->json(['error' => 'Admin tidak ditemukan'], 404);
        }

        // Hapus user yang berelasi dengan admin ini
        $admin->user()->delete();

        // Hapus admin-nya
        $admin->delete();

        return response()->json([
            'message' => 'Admin dan User terkait berhasil dihapus',
        ]);
    }

    public function getDashboardData(Request $request)
    {
      
        $jumlahPendaftar = DB::table('daftar_pelatihan')->count();
        $jumlahPeserta = DB::table('daftar_pelatihan')->where('status', 'diterima')->count();
       
        $jumlahAlumni = DB::table('peserta')->where('status_lulus', 'lulus')->count();
        $totalPelatihan = Pelatihan::count();

      
        $pelatihanData = Pelatihan::with('kategori')->latest()->get();
        $tempatKerjaData = Feedback::with('peserta.user:id,name')
                                    ->whereNotNull('tempat_kerja')
                                    ->where('status', 'ditampilkan')
                                    ->latest()
                                    ->get();

  
        return response()->json([
            'stats' => [
                'jumlahPendaftar' => $jumlahPendaftar,
                'jumlahPeserta' => $jumlahPeserta,
                'totalPelatihan' => $totalPelatihan,
                'jumlahAlumni' => $jumlahAlumni,
            ],
            'tables' => [
                'pelatihan' => $pelatihanData,
                'tempatKerja' => $tempatKerjaData,
            ]
        ]);
    }


}
