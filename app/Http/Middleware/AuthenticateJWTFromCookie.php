<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthenticateJWTFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        // Get token from cookie
        $token = $request->cookie('access_token');
        
        if ($token) {
            try {
                // Set the token for JWTAuth
                JWTAuth::setToken($token);
                
                // Authenticate the user
                $user = JWTAuth::authenticate();
                
                if (!$user) {
                    return response()->json(['message' => 'User not found'], 401);
                }
                
            } catch (JWTException $e) {
                return response()->json(['message' => 'Token is invalid or expired'], 401);
            }
        } else {
            return response()->json(['message' => 'Token not provided'], 401);
        }
        
        return $next($request);
    }
}