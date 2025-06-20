<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Peserta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Username atau password salah.'], 401);
        }


        $customClaims = ['peran' => $user->peran];


        $token = JWTAuth::customClaims($customClaims)->fromUser($user);


        if ($user->peran && in_array($user->peran, ['peserta', 'admin', 'ketua'])) {
            $user->load($user->peran);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user
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

    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'name' => 'required|string',
                'nomor_telp' => 'required|string|min:8|max:12',
                'password' => ['required', 'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
            ]);

            $user = User::create([
                'email' => $data['email'],
                'username' => $data['username'],
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'peran' => 'peserta',
            ]);

            Peserta::create([
                'user_id' => $user->id,
                'nomor_telp' => $data['nomor_telp'],
            ]);


            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function refresh()
    {
        // This will automatically invalidate the old token and return a new one.
        // The refresh token is sent via the Authorization header.
        return response()->json([
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

}
