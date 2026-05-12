<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('role');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('username', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $role));
        }

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $users = $query->orderByDesc('created_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->merge([
            'username' => strtolower(trim((string) $request->input('username', ''))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::create($validated);

        ActivityLog::log('created', "Created user: {$user->name}", $user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('role')),
            'message' => 'User created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with('role')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

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
                Rule::unique('users', 'username')->ignore($id),
            ],
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user->update($validated);

        ActivityLog::log('updated', "Updated user: {$user->name}", $user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->fresh()->load('role')),
            'message' => 'User updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        ActivityLog::log('deleted', "Deleted user: {$name}");

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        ActivityLog::log('updated', "User {$user->name} {$status}", $user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->fresh()->load('role')),
            'message' => "User {$status} successfully.",
        ]);
    }
}
