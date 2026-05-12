<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['tags', 'creator', 'collection'])->whereNull('deleted_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($tag = $request->get('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $tag));
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $creatorScoped = false;
        if ($request->filled('created_by')) {
            $userId = (int) $request->get('created_by');
            if ($userId > 0) {
                $query->where('created_by', $userId)
                    ->whereHas('creator', fn($q) => $q->where('is_active', true));
                $creatorScoped = true;
            }
        }
        if (!$creatorScoped) {
            $creator = strtolower(trim((string) $request->get('creator')));
            if ($creator !== '') {
                $query->whereHas('creator', fn($q) => $q->whereRaw('LOWER(username) = ?', [$creator])->where('is_active', true));
            }
        }

        $sort = $request->get('sort', 'newest');
        $query = match ($sort) {
            'stars' => $query->orderByDesc('stars_count'),
            'name' => $query->orderBy('title'),
            default => $query->orderByDesc('created_at'),
        };

        $perPage = min(50, max(1, (int) $request->get('per_page', 12)));
        $projects = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ProjectResource::collection($projects),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $project = Project::with(['tags', 'creator', 'collection'])->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project),
        ]);
    }
}
