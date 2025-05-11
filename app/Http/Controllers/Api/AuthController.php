<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 0) Invalidate any token already present in the Authorization header.
        try {
            $oldToken = JWTAuth::getToken();
            if ($oldToken) {
                JWTAuth::invalidate($oldToken);
            }
        } catch (\Exception $e) {
            // no token to invalidate, or already invalid — we can ignore
        }

        // 1) Validate incoming fields
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // 2) Load the user by email
        $user = User::where('email', $data['email'])->first();

        // 3) Check: exists, correct password, AND is admin
        if (
            ! $user ||
            ! Hash::check($data['password'], $user->password) ||
            $user->peran !== 'admin'
        ) {
            return response()->json([
                'error' => 'Unauthorized — only admins may log in here'
            ], 403);
        }

        // 4) Issue a fresh JWT for this user
        //    (bypass attempt() since we've already checked)
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => JWTAuth::factory()->getTTL() * 60,
        ]);
    }

    public function logout()
    {
        // Grab the token from the Authorization header…
        $token = JWTAuth::getToken();

        if (! $token) {
            return response()->json(['error' => 'No token provided'], 400);
        }

        // Invalidate it (adds to the blacklist)
        try {
            JWTAuth::invalidate($token);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to invalidate token',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Logged out successfully']);
    }
}
