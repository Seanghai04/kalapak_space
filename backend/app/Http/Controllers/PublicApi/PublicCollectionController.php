<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentCollectionResource;
use App\Http\Resources\ProjectResource;
use App\Models\ContentCollection;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCollectionController extends Controller
{
    public function show(Request $request, string $username, string $slug): JsonResponse
    {
        $username = strtolower(trim($username));

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$username])
            ->where('is_active', true)
            ->firstOrFail();

        $collection = ContentCollection::query()
            ->where('author_id', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $perPage = min(50, max(1, (int) $request->get('per_page', 12)));

        $projects = Project::with(['tags', 'creator', 'collection'])
            ->whereNull('deleted_at')
            ->where('created_by', $user->id)
            ->where('collection_id', $collection->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'collection' => (new ContentCollectionResource($collection))->toArray($request),
                'author' => [
                    'username' => $user->username,
                    'name' => $user->name,
                ],
                'projects' => ProjectResource::collection($projects->items()),
                'meta' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                ],
            ],
        ]);
    }
}
