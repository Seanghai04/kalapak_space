<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', "%{$search}%")
                    ->orWhere('subject_type', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'ilike', "%{$search}%");
                    });
            });
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        $perPage = min($request->get('per_page', 20), 50);
        $logs = $query->orderByDesc('created_at')->paginate($perPage);

        $data = collect($logs->items())->map(function ($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'subject_type' => $log->subject_type ? class_basename($log->subject_type) : null,
                'subject_id' => $log->subject_id,
                'description' => $log->description,
                'properties' => $log->properties,
                'ip_address' => $log->ip_address,
                'user' => $log->user ? [
                    'id' => $log->user->id,
                    'name' => $log->user->name,
                    'avatar' => $log->user->avatar
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($log->user->avatar)
                        : null,
                ] : null,
                'created_at' => $log->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
