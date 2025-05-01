<?php

namespace App\Http\Controllers;

use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.user-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Get the tenant from the domain
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
        
        $tenant = \App\Models\Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();

        if (!$tenant) {
            return back()->withErrors([
                'email' => 'Invalid tenant domain.',
            ]);
        }

        // Set the tenant's database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $user = TenantUser::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Store user in session
            session([
                'user' => $user,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->first_name
            ]);
            
            \Log::info('User login', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->first_name
            ]);
            return redirect()->route('user.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        \Log::info('User logout');
        $request->session()->forget(['user', 'user_id', 'user_email', 'user_name']);
        return redirect()->route('tenant.login');
    }
} 