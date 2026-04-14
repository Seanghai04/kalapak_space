<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyTurnstile
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.turnstile.secret_key');

        if (empty($secret)) {
            return $next($request);
        }

        $token = $request->input('turnstile_token');

        // If no token provided, fail open (widget may not have loaded)
        if (empty($token)) {
            return $next($request);
        }

        try {
            $response = Http::timeout(5)->asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            if (!$response->json('success')) {
                return response()->json([
                    'success' => false,
                    'message' => 'CAPTCHA verification failed. Please try again.',
                ], 422);
            }
        } catch (\Exception $e) {
            // Cloudflare unreachable — fail open to not block users
        }

        return $next($request);
    }
}
