<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InformasiLembaga;

class InformasiLembagaController extends Controller
{
    public function index(){
        return response()->json(InformasiLembaga::all());
    }
}
