<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelatihan;

class PelatihanController extends Controller
{
    public function index() {
        return response()->json(Pelatihan::all());
    }
}
