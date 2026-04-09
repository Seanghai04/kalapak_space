<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'role_applied' => ['required', 'string'],
            'github_url' => ['nullable', 'url'],
            'linkedin_url' => ['nullable', 'url'],
            'portfolio_url' => ['nullable', 'url'],
            'skills' => ['required', 'string'],
            'motivation' => ['required', 'string'],
        ]);

        Application::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully! We will review it soon.',
        ], 201);
    }
}
