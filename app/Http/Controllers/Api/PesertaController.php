<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;

class PesertaController extends Controller
{
    // ✅ GET /api/peserta
    public function index()
    {
        $peserta = Peserta::with(['user', 'pendidikan'])->get();

        return response()->json([
            'message' => 'List of peserta',
            'data' => $peserta
        ]);
    }
}
