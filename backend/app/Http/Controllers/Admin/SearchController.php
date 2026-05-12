<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\BlogPost;
use App\Models\Message;
use App\Models\Application;
use App\Models\TeamMember;
use App\Models\Tag;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\SupabaseStorage;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
            'filter' => 'nullable|string|in:all,users,projects,posts,messages,applications,team,tags,media',
        ]);

        $q = $request->get('q');
        $filter = $request->get('filter', 'all');
        $limit = 5;

        $results = [];

        if (in_array($filter, ['all', 'users'])) {
            $results['users'] = User::with('role')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'ilike', "%{$q}%")
                        ->orWhere('username', 'ilike', "%{$q}%")
                        ->orWhere('email', 'ilike', "%{$q}%");
                })
                ->limit($limit)
                ->get()
                ->map(fn($u) => [
                    'id' => $u->id,
                    'title' => $u->name,
                    'subtitle' => '@' . $u->username . ' · ' . $u->email,
                    'meta' => $u->role?->name ?? 'No role',
                    'avatar' => $u->avatar ? app(SupabaseStorage::class)->url($u->avatar) : null,
                    'is_active' => $u->is_active,
                ]);
        }

        if (in_array($filter, ['all', 'projects'])) {
            $results['projects'] = Project::where('title', 'ilike', "%{$q}%")
                ->orWhere('description', 'ilike', "%{$q}%")
                ->limit($limit)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'subtitle' => $p->status,
                    'meta' => $p->created_at?->format('M d, Y'),
                    'thumbnail' => $p->thumbnail ? app(SupabaseStorage::class)->url($p->thumbnail) : null,
                ]);
        }

        if (in_array($filter, ['all', 'posts'])) {
            $results['posts'] = BlogPost::with('category')
                ->where(function ($query) use ($q) {
                    $query->where('title', 'ilike', "%{$q}%")
                        ->orWhere('excerpt', 'ilike', "%{$q}%");
                })
                ->limit($limit)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'subtitle' => $p->category?->name ?? 'Uncategorized',
                    'meta' => $p->status,
                    'thumbnail' => $p->featured_image ? app(SupabaseStorage::class)->url($p->featured_image) : null,
                ]);
        }

        if (in_array($filter, ['all', 'messages'])) {
            $results['messages'] = Message::where(function ($query) use ($q) {
                $query->where('name', 'ilike', "%{$q}%")
                    ->orWhere('subject', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%");
            })
                ->limit($limit)
                ->get()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'title' => $m->subject ?: $m->name,
                    'subtitle' => $m->email,
                    'meta' => $m->is_read ? 'Read' : 'Unread',
                    'is_read' => $m->is_read,
                ]);
        }

        if (in_array($filter, ['all', 'applications'])) {
            $results['applications'] = Application::where(function ($query) use ($q) {
                $query->where('name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%");
            })
                ->limit($limit)
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'title' => $a->name,
                    'subtitle' => $a->email,
                    'meta' => $a->status,
                ]);
        }

        if (in_array($filter, ['all', 'team'])) {
            $results['team'] = TeamMember::where(function ($query) use ($q) {
                $query->where('name', 'ilike', "%{$q}%")
                    ->orWhere('title', 'ilike', "%{$q}%");
            })
                ->limit($limit)
                ->get()
                ->map(fn($t) => [
                    'id' => $t->id,
                    'title' => $t->name,
                    'subtitle' => $t->title,
                    'avatar' => $t->avatar ? app(SupabaseStorage::class)->url($t->avatar) : null,
                ]);
        }

        if (in_array($filter, ['all', 'tags'])) {
            $results['tags'] = Tag::where('name', 'ilike', "%{$q}%")
                ->limit($limit)
                ->get()
                ->map(fn($t) => [
                    'id' => $t->id,
                    'title' => $t->name,
                    'subtitle' => $t->slug,
                    'color' => $t->color,
                ]);
        }

        // Count totals
        $totals = [];
        foreach ($results as $key => $items) {
            $totals[$key] = count($items);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'totals' => $totals,
        ]);
    }
}
