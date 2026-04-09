<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Message::query();

        if ($request->has('read')) {
            $query->where('is_read', $request->boolean('read'));
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('subject', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $messages = $query->orderByDesc('created_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    public function markRead(int $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        $message->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Message::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }
}
