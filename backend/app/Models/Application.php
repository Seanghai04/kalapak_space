<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'github_url',
        'linkedin_url',
        'role_applied',
        'motivation',
        'skills',
        'portfolio_url',
        'status',
        'admin_notes',
    ];
}
