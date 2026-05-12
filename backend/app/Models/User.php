<?php

namespace App\Models;

use App\Services\SupabaseStorage;
use Cloudinary\Cloudinary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'google_id',
        'github_id',
        'password',
        'role_id',
        'avatar',
        'avatar_disk',
        'bio',
        'github_url',
        'linkedin_url',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function series()
    {
        return $this->hasMany(Series::class, 'author_id');
    }

    public function contentCollections()
    {
        return $this->hasMany(ContentCollection::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role?->name, ['admin', 'superadmin']);
    }

    public function isMember(): bool
    {
        return in_array($this->role?->name, ['member', 'admin', 'superadmin']);
    }

    /**
     * Avatar URL safe for public JSON (never throws; Cloudinary/Supabase misconfig returns null).
     */
    public function publicAvatarUrl(): ?string
    {
        if (! $this->avatar || ! is_string($this->avatar)) {
            return null;
        }
        $avatar = trim($this->avatar);
        if ($avatar === '') {
            return null;
        }
        if (preg_match('/^https?:\/\//i', $avatar)) {
            return $avatar;
        }
        $disk = $this->avatar_disk ?? 'supabase';
        if ($disk === 'cloudinary') {
            $cloudUrl = config('cloudinary.cloud_url');
            if (! is_string($cloudUrl) || trim($cloudUrl) === '') {
                return null;
            }
            try {
                return (new Cloudinary($cloudUrl))->image($avatar)->toUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return app(SupabaseStorage::class)->url($avatar);
    }

    public function setUsernameAttribute(?string $value): void
    {
        $this->attributes['username'] = $value !== null && $value !== ''
            ? strtolower($value)
            : $value;
    }

    /** Lowercase letters, digits, underscore; 3–30 chars; unique. */
    public static function generateUniqueUsername(string $base): string
    {
        $base = strtolower(trim($base));
        $base = preg_replace('/[^a-z0-9_]/', '', str_replace(['-', ' ', '.'], '_', $base)) ?? '';
        $base = trim((string) $base, '_');
        if (strlen($base) < 3) {
            $base = 'user';
        }
        if (strlen($base) > 30) {
            $base = substr($base, 0, 30);
        }
        $candidate = $base;
        $n = 0;
        while (static::query()->where('username', $candidate)->exists()) {
            $n++;
            $suffix = '_' . $n;
            $maxLen = max(1, 30 - strlen($suffix));
            $candidate = substr($base, 0, $maxLen) . $suffix;
        }

        return $candidate;
    }
}
