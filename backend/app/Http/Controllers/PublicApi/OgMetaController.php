<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Response;

class OgMetaController extends Controller
{
    public function blogPost(string $slug): Response
    {
        $baseUrl = rtrim(env('SITE_URL', 'https://kalapak-team.space'), '/');

        $post = BlogPost::with('author')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$post) {
            return $this->fallbackHtml($baseUrl);
        }

        $title = e($post->title);
        $description = e($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 200));
        $image = $post->cover_image ?: 'https://res.cloudinary.com/di1hdlb8k/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png';
        $url = $baseUrl . '/blog/' . $post->slug;
        $authorName = $post->author ? e($post->author->name) : 'Kalapak Code Team';
        $publishedAt = $post->published_at ? $post->published_at->toIso8601String() : '';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>{$title} | Kalapak Code Team</title>
    <meta name="description" content="{$description}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{$url}" />
    <meta property="og:title" content="{$title}" />
    <meta property="og:description" content="{$description}" />
    <meta property="og:image" content="{$image}" />
    <meta property="og:site_name" content="Kalapak Code Team" />
    <meta property="article:author" content="{$authorName}" />
    <meta property="article:published_time" content="{$publishedAt}" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{$title}" />
    <meta name="twitter:description" content="{$description}" />
    <meta name="twitter:image" content="{$image}" />

    <!-- Redirect real users to the SPA -->
    <meta http-equiv="refresh" content="0;url={$url}" />
    <link rel="canonical" href="{$url}" />
</head>
<body>
    <p>Redirecting to <a href="{$url}">{$title}</a>...</p>
</body>
</html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function fallbackHtml(string $baseUrl): Response
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Kalapak Code Team | Modern Tech Solutions from Cambodia</title>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{$baseUrl}" />
    <meta property="og:title" content="Kalapak Code Team | Modern Tech Solutions from Cambodia" />
    <meta property="og:description" content="A passionate collective of full-stack developers from Cambodia, crafting modern web & mobile applications with cutting-edge technology." />
    <meta property="og:image" content="https://res.cloudinary.com/di1hdlb8k/image/upload/q_auto/f_auto/v1775860922/Logo_kalapak_om1ygl.png" />
    <meta http-equiv="refresh" content="0;url={$baseUrl}" />
</head>
<body>
    <p>Redirecting to <a href="{$baseUrl}">Kalapak Code Team</a>...</p>
</body>
</html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
