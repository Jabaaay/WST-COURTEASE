<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetTenantDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (session()->has('tenant') && $tenant = session('tenant')) {
                if (isset($tenant->database_name)) {
                    Config::set('database.connections.tenant.database', $tenant->database_name);
                    DB::purge('tenant');
                } else {
                    Log::warning('Tenant database name not set in session', ['tenant' => $tenant]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error setting tenant database connection: ' . $e->getMessage());
        }

        return $next($request);
    }
}
