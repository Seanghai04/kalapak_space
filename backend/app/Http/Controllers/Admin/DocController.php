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
        try {
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
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Returns a flat list of all docs (id, title, slug, parent_id) for parent-page selectors
    public function all(): JsonResponse
    {
        try {
            $docs = Doc::select('id', 'title', 'slug', 'parent_id', 'category')
                ->orderBy('category')
                ->orderBy('order_num')
                ->get();

            return response()->json(['success' => true, 'data' => $docs]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:docs,slug',
            'content' => 'nullable|string',
            'category' => 'required|string|max:100',
            'order_num' => 'integer|min:0',
            'status' => 'in:draft,published',
            'parent_id' => 'nullable|integer|exists:docs,id',
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['author_id'] = auth()->id();
        $data['content'] = $data['content'] ?? '';

        $sections = $data['sections'] ?? [];
        unset($data['sections']);

        $doc = Doc::create($data);

        foreach ($sections as $i => $section) {
            $doc->sections()->create([
                'heading' => $section['heading'],
                'content' => $section['content'] ?? '',
                'order_num' => $i,
            ]);
        }

        ActivityLog::log('created', "Created doc: {$doc->title}", $doc);

        return response()->json([
            'success' => true,
            'data' => $doc->load(['author', 'sections']),
            'message' => 'Doc created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $doc = Doc::with(['author', 'sections'])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $doc]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $doc = Doc::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:docs,slug,' . $id,
            'content' => 'nullable|string',
            'category' => 'required|string|max:100',
            'order_num' => 'integer|min:0',
            'status' => 'in:draft,published',
            'parent_id' => 'nullable|integer|exists:docs,id',
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Prevent a doc from being its own parent
        if (!empty($data['parent_id']) && (int) $data['parent_id'] === $id) {
            $data['parent_id'] = null;
        }

        $sections = $data['sections'] ?? [];
        unset($data['sections']);

        $doc->update($data);

        // Replace sections atomically
        $doc->sections()->delete();
        foreach ($sections as $i => $section) {
            $doc->sections()->create([
                'heading' => $section['heading'],
                'content' => $section['content'] ?? '',
                'order_num' => $i,
            ]);
        }

        ActivityLog::log('updated', "Updated doc: {$doc->title}", $doc);

        return response()->json([
            'success' => true,
            'data' => $doc->load(['author', 'sections']),
            'message' => 'Doc updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $doc = Doc::findOrFail($id);
        ActivityLog::log('deleted', "Deleted doc: {$doc->title}", $doc);
        $doc->sections()->delete();
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
