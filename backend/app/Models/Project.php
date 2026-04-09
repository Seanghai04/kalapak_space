<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'long_description',
        'cover_image',
        'repo_url',
        'demo_url',
        'tech_stack',
        'status',
        'is_featured',
        'is_open_source',
        'stars_count',
        'forks_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'is_featured' => 'boolean',
            'is_open_source' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
