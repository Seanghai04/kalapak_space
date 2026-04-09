<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\BlogPost;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
                'total_projects' => Project::count(),
                'total_blog_posts' => BlogPost::where('status', 'published')->count(),
                'unread_messages' => Message::where('is_read', false)->count(),
                'pending_applications' => Application::where('status', 'pending')->count(),
            ],
        ]);
    }

    public function activity(): JsonResponse
    {
        $logs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
