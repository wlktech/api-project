<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->createUser($request->all());
        // Generate access token
        $accessToken = JWTAuth::fromUser($user);
        // Generate refresh token
        $refreshToken = RefreshToken::generate($user->id, $request);
        // response data collection
        $data = $this->authService->respondWithToken($accessToken, $refreshToken->token, $user);
        // return response
        return ResponseHelper::created($data, 'User registered successfully');
    }

    /**
     * Login user and return JWT token
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$accessToken = $this->authService->authenticate($credentials)) {
            return ResponseHelper::error('Invalid credentials', 401);
        }
        // Get authenticated user
        $user = JWTAuth::user();
        // Generate refresh token
        $refreshToken = RefreshToken::generate($user->id, $request);
        
        // response data collection
        $data = $this->authService->respondWithToken($accessToken, $refreshToken->token, $user);
        return ResponseHelper::success($data, 'User logged in successfully');
    }

    /**
     * Get the authenticated user
     */
    public function me()
    {
        return ResponseHelper::success(new UserResource(JWTAuth::user()), 'User data retrieved successfully');
    }

    /**
     * Logout user (Invalidate access token and revoke refresh token)
     */
    public function logout(Request $request)
    {
        // Invalidate access token
        JWTAuth::invalidate(JWTAuth::getToken());

        // Revoke refresh token if provided
        if ($request->has('refresh_token')) {
            $refreshToken = RefreshToken::where('token', $request->refresh_token)
                ->where('is_revoked', false)
                ->first();

            if ($refreshToken) {
                $refreshToken->revoke();
            }
        }

        return ResponseHelper::success('', 'Successfully logged out');
    }

    /**
     * Logout from all devices (Revoke all tokens)
     */
    public function logoutAll()
    {
        $user = JWTAuth::user();

        // Invalidate current access token
        JWTAuth::invalidate(JWTAuth::getToken());

        // Revoke all refresh tokens
        $user->revokeAllRefreshTokens();

        return ResponseHelper::success('', 'Successfully logged out from all devices');
    }

    /**
     * Refresh access token using refresh token
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        // Find refresh token
        $refreshToken = RefreshToken::where('token', $request->refresh_token)
            ->where('is_revoked', false)
            ->first();

        // Validate refresh token
        if (!$refreshToken) {
            return ResponseHelper::error('Invalid refresh token', 401);
        }

        if (!$refreshToken->isValid()) {
            return ResponseHelper::error('Refresh token expired or revoked', 401);
        }

        // Get user
        $user = $refreshToken->user;

        // Generate new access token
        $newAccessToken = JWTAuth::fromUser($user);

        // Optional: Generate new refresh token (rotation strategy)
        $newRefreshToken = RefreshToken::generate($user->id, $request);

        // Revoke old refresh token
        $refreshToken->revoke();
        $data = $this->authService->respondWithToken($newAccessToken, $newRefreshToken->token, $user);
        return ResponseHelper::success($data, 'Access token refreshed successfully');
    }

    /**
     * Refresh access token using current access token (JWT built-in)
     */
    public function refreshAccessToken()
    {
        try {
            $newAccessToken = JWTAuth::refresh(JWTAuth::getToken());
            $user = JWTAuth::setToken($newAccessToken)->toUser();

            $data = [
                'access_token' => $newAccessToken,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user,
            ];

            return ResponseHelper::success($data, 'Access token refreshed successfully');
        } catch (TokenExpiredException $e) {
            return ResponseHelper::error('Token has expired and cannot be refreshed', 401);
        } catch (TokenInvalidException $e) {
            return ResponseHelper::error('Invalid token', 401);
        }
    }

    /**
     * Get all active sessions (refresh tokens)
     */
    public function sessions()
    {
        $user = JWTAuth::user();
        $sessions = $user->activeRefreshTokens()
            ->latest()
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'ip_address' => $token->ip_address,
                    'user_agent' => $token->user_agent,
                    'created_at' => $token->created_at,
                    'expires_at' => $token->expires_at,
                    'is_current' => request()->ip() === $token->ip_address,
                ];
            });

        return ResponseHelper::success($sessions);
    }

    /**
     * Revoke a specific session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        $user = JWTAuth::user();
        $refreshToken = $user->refreshTokens()->find($sessionId);

        if (!$refreshToken) {
            return ResponseHelper::error('Session not found', 404);
        }

        $refreshToken->revoke();

        return ResponseHelper::success('', 'Session revoked successfully');
    }

    // change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = JWTAuth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return ResponseHelper::error('Current password is incorrect', 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return ResponseHelper::success('Password changed successfully', 200);
    }
}
