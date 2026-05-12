<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'https://kalapak-team.space');
            return redirect($frontendUrl . '/auth/login?error=google_auth_failed');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id if user exists by email but hasn't linked Google yet
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
            // Update avatar from Google if user doesn't have one
            if (!$user->avatar && $googleUser->getAvatar()) {
                $user->update(['avatar' => $googleUser->getAvatar()]);
            }
            if (!$user->username) {
                $base = Str::before((string) $user->email, '@') ?: Str::slug((string) $user->name, '_') ?: 'user';
                $user->update(['username' => User::generateUniqueUsername($base)]);
            }
        } else {
            // Create new user
            $memberRole = Role::where('name', 'member')->first();
            $email = (string) $googleUser->getEmail();
            $usernameBase = Str::before($email, '@') ?: Str::slug((string) $googleUser->getName(), '_') ?: 'user';
            $user = User::create([
                'name' => $googleUser->getName(),
                'username' => User::generateUniqueUsername($usernameBase),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null,
                'role_id' => $memberRole->id,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $frontendUrl = env('FRONTEND_URL', 'https://kalapak-team.space');

        return redirect($frontendUrl . '/auth/google/callback?token=' . $token);
    }

    public function redirectToGithub(): RedirectResponse
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function handleGithubCallback(): RedirectResponse
    {
        try {
            $githubUser = Socialite::driver('github')->stateless()->user();
        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'https://kalapak-team.space');
            return redirect($frontendUrl . '/auth/login?error=github_auth_failed');
        }

        $user = User::where('github_id', $githubUser->getId())
            ->orWhere('email', $githubUser->getEmail())
            ->first();

        if ($user) {
            if (!$user->github_id) {
                $user->update(['github_id' => $githubUser->getId()]);
            }
            if (!$user->avatar && $githubUser->getAvatar()) {
                $user->update(['avatar' => $githubUser->getAvatar()]);
            }
            if (!$user->username) {
                $base = $githubUser->getNickname()
                    ?: Str::before((string) $user->email, '@')
                    ?: Str::slug((string) $user->name, '_')
                    ?: 'user';
                $user->update(['username' => User::generateUniqueUsername($base)]);
            }
        } else {
            $memberRole = Role::where('name', 'member')->first();
            $email = (string) $githubUser->getEmail();
            $usernameBase = $githubUser->getNickname()
                ?: Str::before($email, '@')
                ?: Str::slug((string) ($githubUser->getName() ?? 'githubuser'), '_');
            $user = User::create([
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'username' => User::generateUniqueUsername($usernameBase),
                'email' => $email,
                'github_id' => $githubUser->getId(),
                'avatar' => $githubUser->getAvatar(),
                'password' => null,
                'role_id' => $memberRole->id,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $frontendUrl = env('FRONTEND_URL', 'https://kalapak-team.space');

        return redirect($frontendUrl . '/auth/github/callback?token=' . $token);
    }
}
