<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TenantLoginRequest;

class TenantAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.tenant-login');
    }

    public function login(TenantLoginRequest $request)
    {
        // Get the tenant from the domain
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
        
        \Log::info('Login attempt', [
            'email' => $request->email,
            'domain' => $domain
        ]);
        
        $tenant = \App\Models\Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();

        if (!$tenant) {
            \Log::warning('Invalid tenant domain', ['domain' => $domain]);
            return back()->withErrors([
                'email' => 'Invalid tenant domain.',
            ]);
        }

        // Set the tenant's database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Try to find a tenant user first
        $tenantUser = \App\Models\TenantUser::where('email', $request->email)->first();
        
        if ($tenantUser) {
            \Log::info('Found tenant user', ['user_id' => $tenantUser->id]);
            
            if (Hash::check($request->password, $tenantUser->password)) {
                \Log::info('User password verified');
                
                // Store user in session
                session([
                    'user' => $tenantUser,
                    'user_id' => $tenantUser->id,
                    'user_email' => $tenantUser->email,
                    'user_name' => $tenantUser->first_name,
                    'address' => $tenantUser->address,
                    'contact_number' => $tenantUser->contact_number,
                    'user_password' => $tenantUser->password
                ]);
                
                \Log::info('User session created', [
                    'user_id' => session('user_id'),
                    'user_name' => session('user_name'),
                    'user_email' => session('user_email'),
                    'address' => session('address'),
                    'contact_number' => session('contact_number'),
                    'user_password' => session('user_password')
                ]);
                
                return redirect()->route('user.dashboard');
            } else {
                \Log::warning('Invalid user password');
            }
        }

        // If no tenant user found, try to find a tenant
        $tenant = \App\Models\Tenant::where('email', $request->email)->first();
        
        if ($tenant) {
            \Log::info('Found tenant', ['tenant_id' => $tenant->id]);
            
            if (Hash::check($request->password, $tenant->password)) {
                \Log::info('Tenant password verified');
                
                // Store tenant in session
                session([
                    'tenant' => $tenant,
                    'tenant_id' => $tenant->id,
                    'tenant_domain' => $tenant->domain,
                    'tenant_email' => $tenant->email,
                    'tenant_database' => $tenant->database_name,
                    'tenant_name' => $tenant->name,
                    'tenant_password' => $tenant->password
                ]);
                
                \Log::info('Tenant session created', [
                    'tenant_id' => session('tenant_id'),
                    'tenant_domain' => session('tenant_domain'),
                    'tenant_name' => session('tenant_name'),
                    'tenant_email' => session('tenant_email'),
                    'tenant_status' => $tenant->status,
                    'tenant_password' => session('tenant_password')
                ]);
                
                return redirect()->route('tenant.dashboard');
            } else {
                \Log::warning('Invalid tenant password');
            }
        }

        // If no tenant user or tenant is found, check for secondary admin
        $secondaryAdmin = \App\Models\SecondaryAdmin::where('email', $request->email)->first();
        if ($secondaryAdmin) {
            \Log::info('Found secondary admin', ['secondary_admin_id' => $secondaryAdmin->id]);
            
            if (Hash::check($request->password, $secondaryAdmin->password)) {
                \Log::info('Secondary admin password verified');
                
                session([
                    'secondary_admin' => $secondaryAdmin,
                    'secondary_admin_id' => $secondaryAdmin->id,
                    'secondary_admin_email' => $secondaryAdmin->email,
                    'secondary_admin_name' => $secondaryAdmin->name,
                    'secondary_admin_role' => $secondaryAdmin->role,
                    'secondary_admin_password' => $secondaryAdmin->password
                ]);
                
                \Log::info('Secondary admin session created', [
                    'secondary_admin_id' => session('secondary_admin_id'),
                    'secondary_admin_email' => session('secondary_admin_email'),
                    'secondary_admin_name' => session('secondary_admin_name'),
                    'secondary_admin_role' => session('secondary_admin_role'),
                    'secondary_admin_password' => session('secondary_admin_password')
                ]);
                
                return redirect()->route('secondary-admin.dashboard');
            } else {
                \Log::warning('Invalid secondary admin password');
            }   
        }

        \Log::warning('No matching credentials found');
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        \Log::info('Tenant logout');
        $request->session()->forget(['tenant', 'tenant_id', 'tenant_domain', 'tenant_database']);
        return redirect()->route('tenant.login');
    }
    public  function secondaryAdminLogout(Request $request)
    {
        \Log::info('Secondary admin logout');
        
        $request->session()->forget(['secondary_admin', 'secondary_admin_id', 'secondary_admin_email', 'secondary_admin_name']);
        return redirect()->route('tenant.login');

    }
}