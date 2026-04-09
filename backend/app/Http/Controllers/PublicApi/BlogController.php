<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::with(['author', 'category'])
            ->withCount('approvedComments')
            ->where('status', 'published')
            ->whereNotNull('published_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('excerpt', 'ilike', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        }

        $posts = $query->orderByDesc('published_at')->paginate(9);

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

    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::with(['author', 'category', 'approvedComments.user', 'approvedComments.replies'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $post->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($post),
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->where('status', 'published')])->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
