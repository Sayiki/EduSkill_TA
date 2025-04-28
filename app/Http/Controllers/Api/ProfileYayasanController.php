<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileYayasan;

class ProfileYayasanController extends Controller
{
    public function index() {
        return response()->json(ProfileYayasan::all());
    }
}
