<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in as a tenant
        if (!session('tenant')) {
            return redirect()->route('tenant.login');
        }

        // Get the current tenant from session
        $currentTenant = session('tenant');

        // Check if tenant is disabled
        if ($currentTenant->status === 'disabled') {
            session()->forget(['tenant', 'tenant_id', 'tenant_domain', 'tenant_database']);
            return redirect()->route('tenant.login')->withErrors([
                'email' => 'Your account has been disabled. Please contact support.'
            ]);
        }

        // If trying to access a specific tenant's domain
        if ($request->route('domain')) {
            // Verify the domain matches the logged-in tenant's domain
            if ($request->route('domain') !== $currentTenant->domain) {
                abort(403, 'Unauthorized access to tenant domain.');
            }
        }

        // Ensure database connection is set to the correct tenant's database
        config(['database.connections.tenant.database' => $currentTenant->database_name]);

        return $next($request);
    }
} 