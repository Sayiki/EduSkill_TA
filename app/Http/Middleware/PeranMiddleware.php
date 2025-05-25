<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class PeranMiddleware
{
    public function handle(Request $request, Closure $next, string $peran): Response
    {
        try {
            // Get the payload from the token. The payload contains our custom claims.
            $payload = JWTAuth::getPayload(JWTAuth::getToken());

            // Check if the 'peran' claim in the token matches the required peran.
            if ($payload->get('peran') !== $peran) {
                return response()->json(['error' => 'Access Denied. You do not have the required role.'], 403);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized. Token is invalid or expired.'], 401);
        }

        return $next($request);
    }
}