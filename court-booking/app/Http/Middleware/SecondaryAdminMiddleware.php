<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecondaryAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('secondary_admin_id')) {
            return redirect()->route('tenant.login');
        }

        return $next($request);
    }
}   