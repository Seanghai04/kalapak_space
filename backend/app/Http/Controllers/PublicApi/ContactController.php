<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        Message::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully. We will get back to you soon!',
        ], 201);
    }
}
