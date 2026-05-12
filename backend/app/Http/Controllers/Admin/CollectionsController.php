<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentCollectionResource;
use App\Models\ContentCollection;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CollectionsController extends Controller
{
    private function isCollectionsSchemaReady(): bool
    {
        return Schema::hasTable('collections') && Schema::hasColumn('projects', 'collection_id');
    }

    private function collectionsSchemaMissingResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Collections storage is not installed. Run database migrations (e.g. php artisan migrate).',
        ], 503);
    }

    private function canMutate(ContentCollection $collection): bool
    {
        $u = auth()->user();

        return $u->isSuperAdmin() || (int) $collection->author_id === (int) $u->id;
    }

    public function index(Request $request): JsonResponse
    {
        if (! $this->isCollectionsSchemaReady()) {
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
        $query = ContentCollection::query()->with('author')->withCount('projects');

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
            'data' => ContentCollectionResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function assignable(Request $request): JsonResponse
    {
        if (! Schema::hasTable('collections')) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $rows = ContentCollection::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'author_id']);

        return response()->json([
            'success' => true,
            'data' => $rows->map(fn (ContentCollection $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'author_id' => $c->author_id,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->isCollectionsSchemaReady()) {
            return $this->collectionsSchemaMissingResponse();
        }

        $uid = (int) auth()->id();

        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('collections', 'name')->where(fn ($q) => $q->where('author_id', $uid)),
            ],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('collections', 'slug')->where(fn ($q) => $q->where('author_id', $uid)),
            ],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['author_id'] = $uid;
        $collection = ContentCollection::create($data);

        return response()->json([
            'success' => true,
            'data' => new ContentCollectionResource($collection->load('author')->loadCount('projects')),
            'message' => 'Collection created.',
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! $this->isCollectionsSchemaReady()) {
            return $this->collectionsSchemaMissingResponse();
        }

        $collection = ContentCollection::findOrFail($id);

        if (! $this->canMutate($collection)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $uid = (int) $collection->author_id;

        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('collections', 'name')->where(fn ($q) => $q->where('author_id', $uid))->ignore($collection->id),
            ],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('collections', 'slug')->where(fn ($q) => $q->where('author_id', $uid))->ignore($collection->id),
            ],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
        ]);

        $collection->update($data);

        return response()->json([
            'success' => true,
            'data' => new ContentCollectionResource($collection->fresh()->load('author')->loadCount('projects')),
            'message' => 'Collection updated.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        if (! $this->isCollectionsSchemaReady()) {
            return $this->collectionsSchemaMissingResponse();
        }

        $collection = ContentCollection::findOrFail($id);

        if (! $this->canMutate($collection)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        Project::query()->where('collection_id', $collection->id)->update(['collection_id' => null]);
        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted. Projects were kept as uncategorized.',
        ]);
    }
}
