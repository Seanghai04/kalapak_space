<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Doc;
use App\Models\DocCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                $query->whereJsonContains('categories', $category);
            }

            $docs = $query->orderBy('category_order')->orderBy('category')->orderBy('order_num')->orderByDesc('created_at')->paginate(20);

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
            $docs = Doc::select('id', 'title', 'slug', 'parent_id', 'doc_menu_id', 'category', 'category_order', 'order_num', 'status')
                ->orderBy('category_order')
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
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:100',
            'doc_menu_id' => 'nullable|integer|exists:doc_menus,id',
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
        $data['categories'] = $data['categories'] ?? ['General'];
        $data['category'] = $data['categories'][0] ?? 'General'; // keep backward compat

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
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:100',
            'doc_menu_id' => 'nullable|integer|exists:doc_menus,id',
            'order_num' => 'integer|min:0',
            'status' => 'in:draft,published',
            'parent_id' => 'nullable|integer|exists:docs,id',
            'sections' => 'nullable|array',
            'sections.*.heading' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['categories'] = $data['categories'] ?? ($doc->categories ?? ['General']);
        $data['category'] = $data['categories'][0] ?? 'General'; // keep backward compat

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
        $categories = DocCategory::orderBy('name')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'doc_count' => Doc::whereJsonContains('categories', $cat->name)->count(),
                ];
            });

        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:doc_categories,name',
        ]);

        $cat = DocCategory::create(['name' => $data['name']]);
        ActivityLog::log('created', "Created doc category: {$cat->name}", null);

        return response()->json([
            'success' => true,
            'data' => ['id' => $cat->id, 'name' => $cat->name, 'doc_count' => 0],
            'message' => 'Category created.',
        ], 201);
    }

    public function renameCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'old_name' => 'required|string|max:100',
            'new_name' => 'required|string|max:100|unique:doc_categories,name',
        ]);

        $cat = DocCategory::where('name', $data['old_name'])->firstOrFail();
        $cat->update(['name' => $data['new_name']]);

        // Update the old_name entry in the categories jsonb array for each doc
        $docs = Doc::whereJsonContains('categories', $data['old_name'])->get();
        $count = $docs->count();
        foreach ($docs as $doc) {
            $cats = array_map(fn($c) => $c === $data['old_name'] ? $data['new_name'] : $c, $doc->categories ?? []);
            $doc->update(['categories' => $cats, 'category' => $cats[0] ?? $data['new_name']]);
        }

        ActivityLog::log('updated', "Renamed category \"{$data['old_name']}\" → \"{$data['new_name']}\" ({$count} docs)", null);

        return response()->json(['success' => true, 'message' => "Renamed. {$count} doc(s) updated."]);
    }

    public function deleteCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'move_to' => 'nullable|string|max:100',
        ]);

        $moveTo = $data['move_to'] ?? 'General';

        // Replace the deleted category with moveTo in each doc's categories array
        $docs = Doc::whereJsonContains('categories', $data['name'])->get();
        $count = $docs->count();
        foreach ($docs as $doc) {
            $cats = array_filter($doc->categories ?? [], fn($c) => $c !== $data['name']);
            if (empty($cats)) {
                $cats = [$moveTo];
            }
            $cats = array_values($cats);
            $doc->update(['categories' => $cats, 'category' => $cats[0]]);
        }

        DocCategory::where('name', $data['name'])->delete();
        DocCategory::firstOrCreate(['name' => $moveTo]);

        ActivityLog::log('deleted', "Deleted category \"{$data['name']}\", moved {$count} doc(s) to \"{$moveTo}\"", null);

        return response()->json(['success' => true, 'message' => "Category deleted. {$count} doc(s) moved to \"{$moveTo}\"."]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:docs,id',
            'items.*.order_num' => 'required|integer|min:0',
            'items.*.doc_menu_id' => 'nullable|integer|exists:doc_menus,id',
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                Doc::where('id', $item['id'])->update([
                    'order_num' => $item['order_num'],
                    'doc_menu_id' => $item['doc_menu_id'] ?? null,
                ]);
            }
        });

        ActivityLog::log('updated', 'Reordered docs', null);

        return response()->json(['success' => true, 'message' => 'Order saved successfully.']);
    }
}
