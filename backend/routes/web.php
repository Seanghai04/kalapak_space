<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\PublicApi\OgMetaController;
use App\Http\Controllers\PublicApi\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Kalapak Code Team API',
        'version' => '1.0.0',
    ]);
});

// Google OAuth (browser redirects, not API calls)
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// GitHub OAuth
Route::get('/auth/github/redirect', [SocialAuthController::class, 'redirectToGithub']);
Route::get('/auth/github/callback', [SocialAuthController::class, 'handleGithubCallback']);

// SEO: Sitemap & Robots.txt
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

// OG Meta: Serve dynamic Open Graph tags for social media crawlers
Route::get('/og/blog/{slug}', [OgMetaController::class, 'blogPost']);
