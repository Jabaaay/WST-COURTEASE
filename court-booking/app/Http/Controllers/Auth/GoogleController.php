<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TenantUser;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            
            // Get the current tenant from session
            if (!session()->has('tenant')) {
                return redirect()->route('tenant.login')->with('error', 'Please login to a tenant first.');
            }

            $tenant = session('tenant');
            
            // Set the tenant database connection
            config(['database.connections.tenant.database' => $tenant->database_name]);
            
            // Find user in tenant database
            $finduser = TenantUser::on('tenant')->where('google_id', $user->id)->first();

            if ($finduser) {
                Auth::guard('tenant')->login($finduser);
                return redirect()->intended('user.dashboard');
            } else {
                $newUser = TenantUser::on('tenant')->create([
                    'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => bcrypt(uniqid()),
                    'address' => null, // Will be updated in profile
                    'contact_number' => null, // Will be updated in profile
                ]);

                Auth::guard('tenant')->login($newUser);
                return redirect()->intended('user.dashboard');
            }
        } catch (Exception $e) {
            \Log::error('Google authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('tenant.login')->with('error', 'Google authentication failed. Please try again.');
        }
    }
} 