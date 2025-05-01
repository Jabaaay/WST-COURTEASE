<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            } elseif ($request->is('tenant/*')) {
                return route('tenant.login');
            } elseif ($request->is('user/*')) {
                return route('user.login');
            }
            return route('login');
        }
        return null;
    }
} 