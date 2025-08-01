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
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;

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
                'username' => 'required|string||min:5|max:100|unique:users,username',
                'name' => 'required|string|min:5|max:100',
                'nomor_telp' => 'required|string|min:10|max:12',
                'password' => ['required', 'confirmed', 'max:20',
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

    public function user() 
    {
        // Memastikan user sudah terautentikasi
        if (!Auth::check()) {
            // Ini akan log jika user tidak terautentikasi, membantu debugging token
            \Log::warning('AuthController@user: User tidak terautentikasi.');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Ambil user yang sedang login
        $user = Auth::user();

        // Eager load relasi 'peserta' jika user adalah peserta
        if ($user->peran === 'peserta') {
            $user->load('peserta');
        }

        // Kembalikan data user dalam format JSON.
        return response()->json([
            'user' => $user
        ], 200);
    }


    public function verifyNow(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'hash' => 'required|string',
            ]);

            $user = User::find($request->input('id'));

            // Check if user exists
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan.',
                    'status' => 'failed'
                ], 404);
            }

            // Generate the expected hash
            $expectedHash = sha1($user->getEmailForVerification());
            $providedHash = $request->input('hash');

            // Debug logging (remove in production)
            \Log::info('Email Verification Debug', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'expected_hash' => $expectedHash,
                'provided_hash' => $providedHash,
                'hash_match' => hash_equals($expectedHash, $providedHash)
            ]);

            // Validate hash
            if (!hash_equals($expectedHash, $providedHash)) {
                return response()->json([
                    'message' => 'Link verifikasi tidak valid atau sudah kadaluarsa.',
                    'status' => 'failed'
                ], 400);
            }

            // Check if already verified
            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Email sudah diverifikasi sebelumnya.',
                    'status' => 'already_verified'
                ], 200);
            }

            // Mark as verified
            if ($user->markEmailAsVerified()) {
                event(new \Illuminate\Auth\Events\Verified($user));
                
                return response()->json([
                    'message' => 'Email berhasil diverifikasi!',
                    'status' => 'success'
                ], 200);
            }

            return response()->json([
                'message' => 'Gagal memverifikasi email.',
                'status' => 'failed'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Email verification error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat memverifikasi email.',
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link verifikasi baru telah dikirim ke email Anda.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // --- UPDATED LOGIC ---
        // Jika user dengan email tersebut tidak ditemukan di database.
        if (!$user) {
            // Kembalikan pesan error yang jelas dengan status 404 (Not Found).
            return response()->json([
                'message' => 'Email yang Anda masukkan tidak terdaftar.'
            ], 404);
        }

        // Jika email user belum terverifikasi.
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email Anda belum terverifikasi. Silakan verifikasi terlebih dahulu.'
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // 1. Buat token reset password secara manual.
            $token = PasswordFacade::broker()->createToken($user);
            
            // 2. Kirim notifikasi ke user (ini akan memanggil metode sendPasswordResetNotification di model User).
            $user->sendPasswordResetNotification($token);

            return response()->json([
                'message' => 'Link reset password telah dikirim ke email Anda.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengirim email reset password: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat mencoba mengirim email.'
            ], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', 'max:20',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
        ]);


        $status = PasswordFacade::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
            
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60)); 

                $user->save();
            }
        );

        if ($status == PasswordFacade::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password berhasil direset. Silakan login dengan password baru Anda.'
            ], 200);
        }


        return response()->json([
            'message' => 'Link reset password sudah kadaluwarsa'
        ], 400);
    }

    public function checkAuth()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json([
                'authenticated' => true,
                'user' => $user
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'authenticated' => false,
                'error' => 'token_invalid'
            ], 401);
        }
    }

    public function changePassword(Request $request)
    {
  
        $user = auth('api')->user();


        $request->validate([
            'current_password' => [
                'required',
                'string',
    
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {

                        $fail('Password saat ini yang Anda masukkan salah.');
                    }
                },
            ],
            'new_password' => [
                'required',
                'string',
                'confirmed', 
                'different:current_password',
                'max:20',
        
                Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
            ],
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();


        return response()->json([
            'message' => 'Password berhasil diubah.'
        ], 200);
    }

}
