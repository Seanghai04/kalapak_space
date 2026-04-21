<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Return null so the middleware never attempts to redirect to a
     * named "login" route (which does not exist in this API-only backend).
     * Laravel's exception handler will then render a proper JSON 401.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
