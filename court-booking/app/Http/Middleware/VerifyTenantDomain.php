<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class VerifyTenantDomain
{
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->route('domain');
        
        // Validate domain format
        if (!preg_match('/^[a-zA-Z0-9-]+$/', $domain)) {
            return response()->view('errors.tenant-not-found', ['domain' => $domain], 404);
        }

        // Check if domain exists and is accepted
        $tenant = Tenant::where('domain', $domain)
                       ->first();

        if (!$tenant) {
            // Log the attempt to access non-existent domain
            \Log::warning('Attempt to access non-existent tenant domain', [
                'domain' => $domain,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->view('errors.tenant-not-found', ['domain' => $domain], 404);
        }

        // Check if tenant is disabled
        if ($tenant->status === 'disabled') {
            \Log::warning('Attempt to access disabled tenant domain', [
                'domain' => $domain,
                'tenant_id' => $tenant->id,
                'ip' => $request->ip()
            ]);
            
            return response()->view('errors.tenant-disabled', [
                'domain' => $domain,
                'tenant' => $tenant
            ], 403);
        }

        // Check if tenant is accepted
        if ($tenant->status !== 'accepted') {
            \Log::warning('Attempt to access non-accepted tenant domain', [
                'domain' => $domain,
                'tenant_id' => $tenant->id,
                'status' => $tenant->status
            ]);
            
            return response()->view('errors.tenant-not-accepted', [
                'domain' => $domain,
                'tenant' => $tenant
            ], 403);
        }

        // Store tenant in request and session for later use
        $request->merge(['tenant' => $tenant]);
        session(['tenant' => $tenant]);
        
        // Set the tenant's database connection
        config(['database.connections.tenant.database' => $tenant->database_name]);
        
        return $next($request);
    }
} 