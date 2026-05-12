<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\SeriesResource;
use App\Models\Series;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicSeriesController extends Controller
{
    public function show(Request $request, string $username, string $slug): JsonResponse
    {
        $username = strtolower(trim($username));

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$username])
            ->where('is_active', true)
            ->firstOrFail();

        $series = Series::query()
            ->where('author_id', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $perPage = min(50, max(1, (int) $request->get('per_page', 12)));

        $posts = BlogPost::with(['author', 'category', 'series'])
            ->withCount('approvedComments')
            ->where('status', 'published')
            ->where('author_id', $user->id)
            ->where('series_id', $series->id)
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'series' => (new SeriesResource($series))->toArray($request),
                'author' => [
                    'username' => $user->username,
                    'name' => $user->name,
                ],
                'posts' => BlogPostResource::collection($posts->items()),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ],
        ]);
    }
}
