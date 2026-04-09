<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::withCount('users')->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $role = Role::create($validated);

        return response()->json([
            'success' => true,
            'data' => $role->loadCount('users'),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $role->update($validated);

        return response()->json([
            'success' => true,
            'data' => $role->loadCount('users'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role with assigned users.',
            ], 422);
        }

        if (in_array($role->name, ['admin', 'member', 'guest'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete system roles.',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }
}
