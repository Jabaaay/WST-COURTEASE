<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TenantAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.tenant-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $tenant = Tenant::where('email', $credentials['email'])
                       ->where('status', 'accepted')
                       ->first();

        if ($tenant && Hash::check($credentials['password'], $tenant->password)) {
            // Store tenant in session with their specific data
            session([
                'tenant' => $tenant,
                'tenant_id' => $tenant->id,
                'tenant_domain' => $tenant->domain,
                'tenant_database' => $tenant->database_name
            ]);
            
            // Switch to tenant's database
            config(['database.connections.tenant.database' => $tenant->database_name]);
            
            return redirect()->route('tenant.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['tenant', 'tenant_id', 'tenant_domain', 'tenant_database']);
        return redirect()->route('tenant.login');
    }
} 