<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'title',
        'bio',
        'avatar',
        'github_url',
        'linkedin_url',
        'telegram_url',
        'email',
        'is_founder',
        'display_order',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_founder' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
