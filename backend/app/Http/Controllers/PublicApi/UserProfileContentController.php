<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\ProjectResource;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Lists published posts / projects for a public profile.
 * Path-based username avoids relying on query params (proxies, bad client values).
 */
class UserProfileContentController extends Controller
{
    public function blogPosts(Request $request, string $username): JsonResponse
    {
        $username = strtolower(trim($username));

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$username])
            ->where('is_active', true)
            ->firstOrFail();

        // Match public post visibility (BlogController@show): status published only.
        // Do not require published_at — some rows are published but published_at was never set.
        $withBlog = ['author', 'category'];
        if (Schema::hasTable('series') && Schema::hasColumn('blog_posts', 'series_id')) {
            $withBlog[] = 'series';
        }

        $query = BlogPost::with($withBlog)
            ->withCount('approvedComments')
            ->where('status', 'published')
            ->where('author_id', $user->id);

        if ($search = $request->get('search')) {
            $search = trim((string) $search);
            if ($search !== '') {
                $pattern = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($q) use ($pattern) {
                    $q->whereRaw('LOWER(title) LIKE LOWER(?)', [$pattern])
                        ->orWhereRaw('LOWER(excerpt) LIKE LOWER(?)', [$pattern]);
                });
            }
        }

        if ($category = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        }

        if ($seriesSlug = $request->get('series')) {
            if (Schema::hasTable('series') && Schema::hasColumn('blog_posts', 'series_id')) {
                $query->whereHas('series', fn ($q) => $q->where('slug', $seriesSlug));
            }
        }

        if (filter_var($request->get('uncategorized'), FILTER_VALIDATE_BOOLEAN)) {
            if (Schema::hasColumn('blog_posts', 'series_id')) {
                $query->whereNull('series_id');
            }
        }

        $perPage = min(50, max(1, (int) $request->get('per_page', 9)));
        $posts = $query->orderByRaw('COALESCE(published_at, created_at) DESC')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => BlogPostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function projects(Request $request, string $username): JsonResponse
    {
        $username = strtolower(trim($username));

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$username])
            ->where('is_active', true)
            ->firstOrFail();

        $withProject = ['tags', 'creator'];
        if (Schema::hasTable('collections') && Schema::hasColumn('projects', 'collection_id')) {
            $withProject[] = 'collection';
        }

        $query = Project::with($withProject)
            ->whereNull('deleted_at')
            ->where('created_by', $user->id);

        if ($search = $request->get('search')) {
            $search = trim((string) $search);
            if ($search !== '') {
                $pattern = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($q) use ($pattern) {
                    $q->whereRaw('LOWER(title) LIKE LOWER(?)', [$pattern])
                        ->orWhereRaw('LOWER(description) LIKE LOWER(?)', [$pattern]);
                });
            }
        }

        if ($tag = $request->get('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $tag));
        }

        if ($collectionSlug = $request->get('collection')) {
            if (Schema::hasTable('collections') && Schema::hasColumn('projects', 'collection_id')) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            }
        }

        if (filter_var($request->get('uncategorized'), FILTER_VALIDATE_BOOLEAN)) {
            if (Schema::hasColumn('projects', 'collection_id')) {
                $query->whereNull('collection_id');
            }
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
}
