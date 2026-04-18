<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Doc;
use Illuminate\Http\JsonResponse;

class DocController extends Controller
{
    public function index(): JsonResponse
    {
        $docs = Doc::where('status', 'published')
            ->select('id', 'title', 'slug', 'category', 'order_num', 'updated_at')
            ->orderBy('category')
            ->orderBy('order_num')
            ->get()
            ->groupBy('category');

        return response()->json(['success' => true, 'data' => $docs]);
    }

    public function show(string $slug): JsonResponse
    {
        $doc = Doc::where('slug', $slug)->where('status', 'published')->firstOrFail();

        return response()->json(['success' => true, 'data' => $doc]);
    }
}
