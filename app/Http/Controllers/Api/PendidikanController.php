<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendidikan;

class PendidikanController extends Controller
{
    public function index() {
        return response()->json(Pendidikan::all());
    }
}
