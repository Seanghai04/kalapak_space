<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'bio' => $this->bio,
            'avatar' => $this->avatar ? Storage::disk('supabase')->url($this->avatar) : null,
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'telegram_url' => $this->telegram_url,
            'email' => $this->email,
            'is_founder' => $this->is_founder,
            'is_visible' => $this->is_visible,
            'display_order' => $this->display_order,
            'created_at' => $this->created_at,
        ];
    }
}
