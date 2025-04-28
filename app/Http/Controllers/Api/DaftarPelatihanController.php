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
}
