<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        
        $semuaAdmin = Admin::with('user')->get(); 

        return response()->json($semuaAdmin);
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


}
