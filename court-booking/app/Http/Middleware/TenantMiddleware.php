<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('tenant') && $tenant = session('tenant')) {
            if (isset($tenant->database_name)) {
                config(['database.connections.tenant.database' => $tenant->database_name]);
            }
        }

        return $next($request);
    }
} 