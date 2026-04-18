<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Doc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Doc::with('author');

        if ($search = $request->get('search')) {
            $query->where('title', 'ilike', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $docs = $query->orderBy('category')->orderBy('order_num')->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $docs->items(),
            'meta' => [
                'current_page' => $docs->currentPage(),
                'last_page' => $docs->lastPage(),
                'per_page' => $docs->perPage(),
                'total' => $docs->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:docs,slug',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'order_num' => 'integer|min:0',
            'status' => 'in:draft,published',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['author_id'] = auth()->id();

        $doc = Doc::create($data);

        ActivityLog::log('created', "Created doc: {$doc->title}", $doc);

        return response()->json([
            'success' => true,
            'data' => $doc->load('author'),
            'message' => 'Doc created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $doc = Doc::with('author')->findOrFail($id);

        return response()->json(['success' => true, 'data' => $doc]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $doc = Doc::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:docs,slug,' . $id,
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'order_num' => 'integer|min:0',
            'status' => 'in:draft,published',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $doc->update($data);

        ActivityLog::log('updated', "Updated doc: {$doc->title}", $doc);

        return response()->json([
            'success' => true,
            'data' => $doc->load('author'),
            'message' => 'Doc updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $doc = Doc::findOrFail($id);
        ActivityLog::log('deleted', "Deleted doc: {$doc->title}", $doc);
        $doc->delete();

        return response()->json(['success' => true, 'message' => 'Doc deleted.']);
    }

    public function categories(): JsonResponse
    {
        $categories = Doc::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
