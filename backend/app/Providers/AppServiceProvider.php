<?php

namespace App\Providers;

use App\Models\BlogCategory;
use App\Models\Project;
use App\Models\Tag;
use App\Observers\BlogCategoryObserver;
use App\Observers\ProjectObserver;
use App\Observers\TagObserver;
use App\Services\SupabaseStorage;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SupabaseStorage::class);
    }

    public function boot(): void
    {
        Project::observe(ProjectObserver::class);
        Tag::observe(TagObserver::class);
        BlogCategory::observe(BlogCategoryObserver::class);

        // General API: 60 requests per minute per IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Login/Register: 5 attempts per minute per IP (brute force protection)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Contact/Applications: 3 per minute per IP (spam protection)
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Uploads: 10 per minute per user
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
