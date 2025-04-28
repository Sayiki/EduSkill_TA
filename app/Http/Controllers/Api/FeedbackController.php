<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index() {
        return response()->json(Feedback::with('peserta')->get());
    }
}
