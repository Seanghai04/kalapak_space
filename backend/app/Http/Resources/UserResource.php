<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ? Storage::disk('supabase')->url($this->avatar) : null,
            'bio' => $this->bio,
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'is_active' => $this->is_active,
            'role' => $this->role ? [
                'id' => $this->role->id,
                'name' => $this->role->name,
                'display_name' => $this->role->display_name,
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
