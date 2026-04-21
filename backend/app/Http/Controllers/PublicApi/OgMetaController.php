<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\SupabaseStorage;
use Illuminate\Http\Response;

class OgMetaController extends Controller
{
    private const FALLBACK_IMAGE = 'https://res.cloudinary.com/kalapak/image/upload/c_pad,w_1200,h_630,b_rgb:111827,g_center/q_auto/f_png/v1775860922/Logo_kalapak_om1ygl.png';

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
        $image = $this->resolveOgImage($post);
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

    <!-- Open Graph (Facebook, Telegram, WhatsApp, LinkedIn, Viber, Zalo, Line) -->
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{$url}" />
    <meta property="og:title" content="{$title}" />
    <meta property="og:description" content="{$description}" />
    <meta property="og:image" content="{$image}" />
    <meta property="og:image:secure_url" content="{$image}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt" content="{$title}" />
    <meta property="og:site_name" content="Kalapak Code Team" />
    <meta property="og:locale" content="km_KH" />
    <meta property="og:locale:alternate" content="en_US" />
    <meta property="article:author" content="{$authorName}" />
    <meta property="article:published_time" content="{$publishedAt}" />

    <!-- X / Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@KalapakTeam" />
    <meta name="twitter:title" content="{$title}" />
    <meta name="twitter:description" content="{$description}" />
    <meta name="twitter:image" content="{$image}" />
    <meta name="twitter:image:alt" content="{$title}" />

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

    /**
     * Resolve the OG image URL for a blog post.
     * - Cloudinary posts: apply c_fill,w_1200,h_630,g_auto transformation for proper OG dimensions.
     * - Supabase posts: return the public URL as-is.
     * - No cover image: return the branded fallback.
     */
    private function resolveOgImage(BlogPost $post): string
    {
        if (!$post->cover_image) {
            return self::FALLBACK_IMAGE;
        }

        if (($post->storage_provider ?? 'supabase') === 'cloudinary') {
            // cover_image is already a full Cloudinary URL; inject OG transformation
            return preg_replace(
                '#/upload/#',
                '/upload/c_fill,w_1200,h_630,g_auto,q_auto,f_jpg/',
                $post->cover_image,
                1
            ) ?: $post->cover_image;
        }

        return app(SupabaseStorage::class)->url($post->cover_image) ?? self::FALLBACK_IMAGE;
    }

    private function fallbackHtml(string $baseUrl): Response
    {
        $image = self::FALLBACK_IMAGE;
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
    <meta property="og:image" content="{$image}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
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
