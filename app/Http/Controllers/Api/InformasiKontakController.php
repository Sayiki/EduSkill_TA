<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InformasiKontak;

class InformasiKontakController extends Controller
{
    public function index() {
        return response()->json(InformasiKontak::all());
    }
}
