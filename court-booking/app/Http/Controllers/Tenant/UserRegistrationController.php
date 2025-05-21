<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UserRegistrationRequest;

class UserRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.tenant-register-user');
    }

    public function register(UserRegistrationRequest $request)
    {
        // Get the tenant from the domain
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
        
        Log::info('Attempting user registration', [
            'domain' => $domain,
            'email' => $request->email
        ]);

        $tenant = Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();

        if (!$tenant) {
            Log::error('Tenant not found or not accepted', [
                'domain' => $domain
            ]);
            return redirect()->back()->with('error', 'Invalid tenant domain.');
        }

        if (!isset($tenant->database_name)) {
            Log::error('Tenant database not configured', [
                'tenant' => $tenant
            ]);
            return redirect()->back()->with('error', 'Tenant database not configured properly.');
        }

        // Set the tenant's database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        Log::info('Database connection configured', [
            'connection' => 'tenant',
            'database' => $tenant->database_name,
            'current_database' => DB::connection('tenant')->getDatabaseName()
        ]);

        try {
            $user = TenantUser::create([
                'first_name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'address' => $request->address,
                'contact_number' => $request->contact_number,
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'database' => DB::connection('tenant')->getDatabaseName()
            ]);

            return redirect()->route('tenant.login')->with('success', 'Registration successful! Please login.');
        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'connection' => TenantUser::getConnectionName(),
                'database' => DB::connection('tenant')->getDatabaseName()
            ]);
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }
}
