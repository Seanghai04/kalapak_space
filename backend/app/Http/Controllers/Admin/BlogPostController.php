<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\ActivityLog;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::with(['author', 'category']);

        if ($search = $request->get('search')) {
            $query->where('title', 'ilike', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->get('category_id')) {
            $query->where('category_id', $category);
        }

        $posts = $query->orderByDesc('created_at')->paginate(15);

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

    public function store(BlogPostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('blog', 'supabase');
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);

        ActivityLog::log('created', "Created blog post: {$post->title}", $post);

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($post->load(['author', 'category'])),
            'message' => 'Blog post created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $post = BlogPost::with(['author', 'category'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($post),
        ]);
    }

    public function update(BlogPostRequest $request, int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('supabase')->delete($post->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('blog', 'supabase');
        }

        if ($data['status'] === 'published' && !$post->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);

        ActivityLog::log('updated', "Updated blog post: {$post->title}", $post);

        return response()->json([
            'success' => true,
            'data' => new BlogPostResource($post->fresh()->load(['author', 'category'])),
            'message' => 'Blog post updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);
        $title = $post->title;
        $post->delete();

        ActivityLog::log('deleted', "Deleted blog post: {$title}");

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully.',
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => BlogCategory::all(),
        ]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:blog_categories,slug'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $category = BlogCategory::create($data);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully.',
        ], 201);
    }

    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $category = BlogCategory::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:blog_categories,slug,' . $id],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category updated successfully.',
        ]);
    }

    public function destroyCategory(int $id): JsonResponse
    {
        BlogCategory::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
