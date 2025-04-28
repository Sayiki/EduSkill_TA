<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ketua;

class KetuaController extends Controller
{
    public function index(){
        return response()->json(Ketua::all());
    }
}
