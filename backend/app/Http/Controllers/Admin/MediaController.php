<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Media::with('uploader')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('original_name', 'ilike', "%{$search}%");
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'document') {
                $query->where('mime_type', 'not like', 'image/%');
            }
        }

        $media = $query->paginate($request->input('per_page', 24));

        $items = collect($media->items())->map(function ($item) {
            $data = $item->toArray();
            $data['url'] = Storage::disk($item->disk)->url($item->path);
            if ($item->uploader) {
                $data['uploader'] = [
                    'id' => $item->uploader->id,
                    'name' => $item->uploader->name,
                ];
            }
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'per_page' => $media->perPage(),
                'total' => $media->total(),
            ],
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,pdf'],
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $filename, 'public');

        $media = Media::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        $responseData = $media->toArray();
        $responseData['url'] = Storage::disk('public')->url($path);

        return response()->json([
            'success' => true,
            'data' => $responseData,
            'message' => 'File uploaded successfully.',
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $media = Media::findOrFail($id);
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.',
        ]);
    }
}
