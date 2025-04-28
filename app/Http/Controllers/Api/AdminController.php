<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index() {
        return response()->json(['data' => Admin::with('user')->get()]);
    }
}
