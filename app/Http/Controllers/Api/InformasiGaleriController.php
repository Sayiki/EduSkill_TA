<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InformasiGaleri;

class InformasiGaleriController extends Controller
{
    public function index() {
        return response()->json(InformasiGaleri::all());
    }
}
