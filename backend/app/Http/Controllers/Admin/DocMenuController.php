<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doc;
use App\Models\DocMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocMenuController extends Controller
{
    /**
     * Return the full hierarchical tree: Main Menus with their Sub-Menus.
     * Each menu node includes a doc_count.
     */
    public function index(): JsonResponse
    {
        try {
            $mainMenus = DocMenu::whereNull('parent_id')
                ->orderBy('order_num')
                ->orderBy('name')
                ->with([
                    'children' => function ($q) {
                        $q->orderBy('order_num')->orderBy('name');
                    }
                ])
                ->get()
                ->map(function ($menu) {
                    $childIds = $menu->children->pluck('id')->toArray();
                    $directCount = Doc::where('doc_menu_id', $menu->id)->count();
                    $childCount = count($childIds) > 0
                        ? Doc::whereIn('doc_menu_id', $childIds)->count()
                        : 0;
                    $menu->doc_count = $directCount + $childCount;

                    $menu->children = $menu->children->map(function ($child) {
                        $child->doc_count = Doc::where('doc_menu_id', $child->id)->count();
                        return $child;
                    });

                    return $menu;
                });

            return response()->json(['success' => true, 'data' => $mainMenus]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Flat list of all menus — used by the doc form to populate the menu selector.
     */
    public function flat(): JsonResponse
    {
        try {
            $menus = DocMenu::orderBy('order_num')->orderBy('name')->get(['id', 'name', 'slug', 'parent_id']);
            return response()->json(['success' => true, 'data' => $menus]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:100|unique:doc_menus,name',
                'description' => 'nullable|string|max:500',
                'order_num' => 'integer|min:0',
                'parent_id' => 'nullable|integer|exists:doc_menus,id',
            ]);

            $data['slug'] = Str::slug($data['name']);
            $data['order_num'] = $data['order_num'] ?? 0;

            $menu = DocMenu::create($data);

            return response()->json(['success' => true, 'data' => $menu, 'message' => 'Menu created.'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $menu = DocMenu::findOrFail($id);

            $data = $request->validate([
                'name' => 'required|string|max:100|unique:doc_menus,name,' . $id,
                'description' => 'nullable|string|max:500',
                'order_num' => 'integer|min:0',
                'parent_id' => 'nullable|integer|exists:doc_menus,id',
            ]);

            if (!empty($data['parent_id']) && $data['parent_id'] == $id) {
                return response()->json(['success' => false, 'message' => 'A menu cannot be its own parent.'], 422);
            }

            $data['slug'] = Str::slug($data['name']);
            $menu->update($data);

            $menu->doc_count = Doc::where('doc_menu_id', $menu->id)->count();

            return response()->json(['success' => true, 'data' => $menu, 'message' => 'Menu updated.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $menu = DocMenu::findOrFail($id);
            $menu->delete();

            return response()->json(['success' => true, 'message' => 'Menu deleted.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reorder(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:doc_menus,id',
                'items.*.order_num' => 'required|integer|min:0',
                'items.*.parent_id' => 'nullable|integer|exists:doc_menus,id',
            ]);

            DB::transaction(function () use ($data) {
                foreach ($data['items'] as $item) {
                    DocMenu::where('id', $item['id'])->update([
                        'order_num' => $item['order_num'],
                        'parent_id' => $item['parent_id'] ?? null,
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Menu order saved.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
