<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StatusLamaran;

class StatusLamaranController extends Controller
{
    public function index(){
        return response()->json(StatusLamaran::all());
    }
}
