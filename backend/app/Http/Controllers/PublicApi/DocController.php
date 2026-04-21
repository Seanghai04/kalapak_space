<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Doc;
use App\Models\DocMenu;
use Illuminate\Http\JsonResponse;

class DocController extends Controller
{
    /**
     * Navigation tree for the public docs sidebar.
     * Structure: Main Menu → Sub-Menus → Pages → Subpages
     */
    public function nav(): JsonResponse
    {
        $mainMenus = DocMenu::whereNull('parent_id')
            ->orderBy('order_num')
            ->orderBy('name')
            ->with([
                'children' => function ($q) {
                    $q->orderBy('order_num')->orderBy('name')
                        ->with([
                            'pages' => function ($q2) {
                                $q2->where('status', 'published')
                                    ->whereNull('parent_id')
                                    ->select('id', 'title', 'slug', 'doc_menu_id', 'order_num', 'updated_at')
                                    ->orderBy('order_num')
                                    ->with([
                                        'children' => function ($q3) {
                                            $q3->where('status', 'published')
                                                ->select('id', 'title', 'slug', 'parent_id', 'order_num', 'updated_at')
                                                ->orderBy('order_num');
                                        }
                                    ]);
                            }
                        ]);
                },
                'pages' => function ($q) {
                    // Pages directly under the main menu (no sub-menu)
                    $q->where('status', 'published')
                        ->whereNull('parent_id')
                        ->select('id', 'title', 'slug', 'doc_menu_id', 'order_num', 'updated_at')
                        ->orderBy('order_num')
                        ->with([
                            'children' => function ($q2) {
                        $q2->where('status', 'published')
                            ->select('id', 'title', 'slug', 'parent_id', 'order_num', 'updated_at')
                            ->orderBy('order_num');
                    }
                        ]);
                }
            ])
            ->get();

        return response()->json(['success' => true, 'data' => $mainMenus]);
    }

    /**
     * Legacy flat endpoint — grouped by doc_menu name for backward compat.
     */
    public function index(): JsonResponse
    {
        $docs = Doc::where('status', 'published')
            ->whereNull('parent_id')
            ->with([
                'docMenu',
                'children' => fn($q) => $q
                    ->where('status', 'published')
                    ->select('id', 'title', 'slug', 'parent_id', 'order_num', 'updated_at')
                    ->orderBy('order_num')
                    ->with([
                        'children' => fn($q2) => $q2
                            ->where('status', 'published')
                            ->select('id', 'title', 'slug', 'parent_id', 'order_num', 'updated_at')
                            ->orderBy('order_num')
                    ])
            ])
            ->select('id', 'title', 'slug', 'category', 'category_order', 'order_num', 'parent_id', 'doc_menu_id', 'updated_at')
            ->orderBy('category_order')
            ->orderBy('order_num')
            ->get()
            ->groupBy(fn($d) => $d->docMenu?->name ?? $d->category ?? 'General');

        return response()->json(['success' => true, 'data' => $docs]);
    }

    public function show(string $slug): JsonResponse
    {
        $doc = Doc::where('slug', $slug)
            ->where('status', 'published')
            ->with(['sections' => fn($q) => $q->orderBy('order_num')])
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $doc]);
    }
}

