<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['tags', 'creator']);

        if ($search = $request->get('search')) {
            $query->where('title', 'ilike', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $projects = $query->orderByDesc('created_at')->paginate(15);

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

    public function store(ProjectRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        }

        $data['created_by'] = auth()->id();
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $project = Project::create($data);
        if ($tags) {
            $project->tags()->sync($tags);
        }

        ActivityLog::log('created', "Created project: {$project->title}", $project);

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project->load(['tags', 'creator'])),
            'message' => 'Project created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $project = Project::with(['tags', 'creator'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project),
        ]);
    }

    public function update(ProjectRequest $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        }

        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $project->update($data);
        if (isset($request->tags)) {
            $project->tags()->sync($tags);
        }

        ActivityLog::log('updated', "Updated project: {$project->title}", $project);

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project->fresh()->load(['tags', 'creator'])),
            'message' => 'Project updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $title = $project->title;
        $project->delete();

        ActivityLog::log('deleted', "Deleted project: {$title}");

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully.',
        ]);
    }
}
