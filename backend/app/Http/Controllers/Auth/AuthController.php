<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Inline rate limiting: 5 attempts per minute per IP
            $key = 'login-attempt:' . $request->ip();
            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "អ្នកបានព្យាយាមច្រើនដងពេកហើយ សូមរង់ចាំ $seconds វិនាទីទៀត!",
                ], 429);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                \Illuminate\Support\Facades\RateLimiter::hit($key, 60); // 60s decay
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated.',
                ], 403);
            }

            \Illuminate\Support\Facades\RateLimiter::clear($key);
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new UserResource($user->load('role')),
                    'token' => $token,
                ],
                'message' => 'Login successful.',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Login error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load('role')),
        ]);
    }
}
