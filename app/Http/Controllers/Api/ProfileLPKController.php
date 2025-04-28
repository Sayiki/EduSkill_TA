<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileLPK;

class ProfileLPKController extends Controller
{
    public function index() {
        return response()->json(ProfileLPK::all());
    }
}
