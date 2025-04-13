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
                       ->where('status', 'accepted')
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

        // Store tenant in request for later use
        $request->merge(['tenant' => $tenant]);
        
        return $next($request);
    }
} 