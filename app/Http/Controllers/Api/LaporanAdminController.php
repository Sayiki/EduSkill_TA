<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanAdmin;

class LaporanAdminController extends Controller
{
    public function index() {
        return response()->json(LaporanAdmin::all());
    }
}
