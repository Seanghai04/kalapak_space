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
        $query = Project::with(['tags', 'creator'])->whereNull('deleted_at');

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

        $sort = $request->get('sort', 'newest');
        $query = match ($sort) {
            'stars' => $query->orderByDesc('stars_count'),
            'name' => $query->orderBy('title'),
            default => $query->orderByDesc('created_at'),
        };

        $projects = $query->paginate(12);

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
        $project = Project::with(['tags', 'creator'])->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project),
        ]);
    }
}
