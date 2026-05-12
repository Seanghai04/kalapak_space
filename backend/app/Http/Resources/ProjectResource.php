<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\SupabaseStorage;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'long_description' => $this->long_description,
            'cover_image' => $this->cover_image ? app(SupabaseStorage::class)->url($this->cover_image) : null,
            'repo_url' => $this->repo_url,
            'demo_url' => $this->demo_url,
            'tech_stack' => $this->tech_stack,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_open_source' => $this->is_open_source,
            'stars_count' => $this->stars_count,
            'forks_count' => $this->forks_count,
            'tags' => $this->whenLoaded('tags', fn() => $this->tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'color' => $tag->color,
            ])),
            'collection' => $this->whenLoaded('collection', fn() => $this->collection
                ? [
                    'id' => $this->collection->id,
                    'name' => $this->collection->name,
                    'slug' => $this->collection->slug,
                ]
                : null),
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'username' => $this->creator->username,
                'avatar' => $this->creator->publicAvatarUrl(),
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
