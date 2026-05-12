<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $memberRole = Role::where('name', 'member')->first();
        if (!$memberRole) {
            (new RoleSeeder)->run();
            $memberRole = Role::where('name', 'member')->first();
        }
        if (!$memberRole) {
            return response()->json([
                'success' => false,
                'message' => 'Registration is temporarily unavailable. Member role is not configured.',
            ], 503);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $memberRole->id,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user->load('role')),
                'token' => $token,
            ],
            'message' => 'Registration successful.',
        ], 201);
    }
}
