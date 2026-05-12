<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\SupabaseStorage;
use Cloudinary\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
        $request->merge([
            'username' => strtolower(trim((string) $request->input('username', ''))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('users', 'username')->ignore($request->user()->id),
            ],
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
            'storage_provider' => ['nullable', 'in:supabase,cloudinary'],
        ]);

        $user = $request->user();
        $provider = $request->input('storage_provider', 'supabase');

        try {
            // Delete old avatar from the correct provider
            if ($user->avatar) {
                if ($user->avatar_disk === 'cloudinary') {
                    $this->cloudinary()->uploadApi()->destroy($user->avatar);
                } else {
                    app(SupabaseStorage::class)->delete($user->avatar);
                }
            }

            if ($provider === 'cloudinary') {
                $result = $this->cloudinary()->uploadApi()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'kalapak/avatars',
                    'resource_type' => 'image',
                ]);
                $path = $result['public_id'];
            } else {
                $path = app(SupabaseStorage::class)->upload($request->file('avatar'), 'avatars');
            }

            $user->update(['avatar' => $path, 'avatar_disk' => $provider]);

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

    private function cloudinary(): Cloudinary
    {
        return new Cloudinary(config('cloudinary.cloud_url'));
    }
}
