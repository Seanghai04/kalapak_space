<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    private const RESOURCES = ['projects', 'categories', 'tags'];

    /** GET /admin/users/{id}/permissions */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $stored = UserPermission::where('user_id', $id)
            ->get()
            ->keyBy('resource');

        $result = [];
        foreach (self::RESOURCES as $resource) {
            $row = $stored->get($resource);
            $result[$resource] = [
                'can_create' => $row?->can_create ?? false,
                'can_update' => $row?->can_update ?? false,
                'can_delete' => $row?->can_delete ?? false,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /** PUT /admin/users/{id}/permissions */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*.resource' => ['required', 'in:' . implode(',', self::RESOURCES)],
            'permissions.*.can_create' => ['boolean'],
            'permissions.*.can_update' => ['boolean'],
            'permissions.*.can_delete' => ['boolean'],
        ]);

        foreach ($validated['permissions'] as $perm) {
            UserPermission::updateOrCreate(
                ['user_id' => $id, 'resource' => $perm['resource']],
                [
                    'can_create' => $perm['can_create'] ?? false,
                    'can_update' => $perm['can_update'] ?? false,
                    'can_delete' => $perm['can_delete'] ?? false,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully.',
        ]);
    }
}
