<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load('role')),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'github_url' => ['nullable', 'url'],
            'linkedin_url' => ['nullable', 'url'],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->fresh()->load('role')),
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $request->user()->update(['password' => $request->password]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();
        $storage = app(SupabaseStorage::class);

        try {
            if ($user->avatar) {
                $storage->delete($user->avatar);
            }

            $path = $storage->upload($request->file('avatar'), 'avatars');
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'data' => new UserResource($user->fresh()->load('role')),
                'message' => 'Avatar uploaded successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Avatar upload failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Storage error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
