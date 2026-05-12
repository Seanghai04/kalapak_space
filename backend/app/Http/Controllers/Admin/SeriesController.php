<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SeriesController extends Controller
{
    private function isSeriesSchemaReady(): bool
    {
        return Schema::hasTable('series') && Schema::hasColumn('blog_posts', 'series_id');
    }

    private function seriesSchemaMissingResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Series storage is not installed. Run database migrations (e.g. php artisan migrate).',
        ], 503);
    }

    private function canMutate(Series $series): bool
    {
        $u = auth()->user();

        return $u->isSuperAdmin() || (int) $series->author_id === (int) $u->id;
    }

    public function index(Request $request): JsonResponse
    {
        if (! $this->isSeriesSchemaReady()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                ],
            ]);
        }

        $user = $request->user();
        $query = Series::query()->with('author')->withCount('blogPosts');

        if (! $user->isSuperAdmin()) {
            $query->where('author_id', $user->id);
        }

        if ($search = $request->get('search')) {
            $search = trim((string) $search);
            if ($search !== '') {
                $pattern = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($q) use ($pattern) {
                    $q->whereRaw('LOWER(name) LIKE LOWER(?)', [$pattern])
                        ->orWhereRaw('LOWER(slug) LIKE LOWER(?)', [$pattern]);
                });
            }
        }

        $items = $query->orderByDesc('created_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => SeriesResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * Flat list for blog post editor (all authors — superadmin / admin workflow).
     */
    public function assignable(Request $request): JsonResponse
    {
        if (! Schema::hasTable('series')) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $rows = Series::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'author_id']);

        return response()->json([
            'success' => true,
            'data' => $rows->map(fn (Series $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'slug' => $s->slug,
                'author_id' => $s->author_id,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->isSeriesSchemaReady()) {
            return $this->seriesSchemaMissingResponse();
        }

        $uid = (int) auth()->id();

        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('series', 'name')->where(fn ($q) => $q->where('author_id', $uid)),
            ],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('series', 'slug')->where(fn ($q) => $q->where('author_id', $uid)),
            ],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['author_id'] = $uid;
        $series = Series::create($data);

        return response()->json([
            'success' => true,
            'data' => new SeriesResource($series->load('author')->loadCount('blogPosts')),
            'message' => 'Series created.',
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->isSeriesSchemaReady()) {
            return $this->seriesSchemaMissingResponse();
        }

        $series = Series::findOrFail($id);

        if (! $this->canMutate($series)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $uid = (int) $series->author_id;

        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('series', 'name')->where(fn ($q) => $q->where('author_id', $uid))->ignore($series->id),
            ],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('series', 'slug')->where(fn ($q) => $q->where('author_id', $uid))->ignore($series->id),
            ],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
        ]);

        $series->update($data);

        return response()->json([
            'success' => true,
            'data' => new SeriesResource($series->fresh()->load('author')->loadCount('blogPosts')),
            'message' => 'Series updated.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (! $this->isSeriesSchemaReady()) {
            return $this->seriesSchemaMissingResponse();
        }

        $series = Series::findOrFail($id);

        if (! $this->canMutate($series)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        BlogPost::query()->where('series_id', $series->id)->update(['series_id' => null]);
        $series->delete();

        return response()->json([
            'success' => true,
            'message' => 'Series deleted. Posts were kept as uncategorized.',
        ]);
    }
}
