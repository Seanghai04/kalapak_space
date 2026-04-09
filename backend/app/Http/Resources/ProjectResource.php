<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'cover_image' => $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null,
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
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'avatar' => $this->creator->avatar ? Storage::disk('public')->url($this->creator->avatar) : null,
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
