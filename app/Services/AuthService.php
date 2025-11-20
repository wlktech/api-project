<?php

namespace App\Services;

use App\Http\Resources\System\UserResource;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Create a new user account
     * 
     * @param array $request User registration data
     * @return User The created user instance
     */

    public function createUser($request)
    {
        try{
            DB::beginTransaction();
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
            if (isset($request['role_id'])) {
                $user->assignRole($request['role_id']);
            }
            DB::commit();
            return $user->fresh('roles');
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
    }

    /**
     * Authenticate a user
     * 
     * @param array $request User login data
     * @return bool True if authentication is successful, false otherwise
     */

    public function authenticate($request)
    {
        return JWTAuth::attempt([
            'email' => $request['email'],
            'password' => $request['password'],
        ]);
    }

    /**
     * Get the token array structure
     * @param string $accessToken
     * @param string $refreshToken
     * @param User $user
     */
    public function respondWithToken1($accessToken, $refreshToken = null, $user = null)
    {
        $data = [
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => new UserResource($user ?? JWTAuth::user()),
        ];

        if ($refreshToken) {
            $data['refresh_token'] = $refreshToken;

            $refreshTokenModel = RefreshToken::where('token', $refreshToken)->first();
            if ($refreshTokenModel) {
                $data['refresh_expires_in'] = now()->diffInSeconds($refreshTokenModel->expires_at);
            }
        }
        return $data;
    }

    public function respondWithToken($accessToken, $refreshToken)
    {
        // Create HttpOnly cookies
        $accessCookie = cookie(
            'access_token',
            $accessToken,
            config('jwt.ttl'), // 60 minutes
            '/',
            null,
            true, // Secure (HTTPS only)
            true, // HttpOnly
            true,
            // 'strict' // SameSite
        );

        $refreshCookie = cookie(
            'refresh_token',
            $refreshToken,
            config('jwt.refresh_ttl'), // 2 weeks
            '/',
            null,
            true, // Secure (HTTPS only)
            true, // HttpOnly
            true,
            // 'strict'
        );

        return response()->json([
            'message' => 'Success',
            'access_token' => $accessToken, // Optional: return in body for initial storage
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ])
        ->withCookie($accessCookie)
        ->withCookie($refreshCookie);
    }
}
