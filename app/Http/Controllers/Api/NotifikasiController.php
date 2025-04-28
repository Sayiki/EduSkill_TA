<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index() {
        return response()->json(Notifikasi::all());
    }
}
