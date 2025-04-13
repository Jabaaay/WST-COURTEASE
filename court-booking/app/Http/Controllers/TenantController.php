<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'domain' => 'required|string|unique:tenants',
        ]);

        Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your application has been submitted successfully!');
    }

    public function accept($id)
    {
        $tenant = Tenant::findOrFail($id);
        $password = Str::random(10);
        $databaseName = 'tenant_' . str_replace(['.', '-'], '_', $tenant->domain);

        // Create database for the tenant
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            
            // Update tenant with database name
            $tenant->update([
                'status' => 'accepted',
                'password' => bcrypt($password),
                'database_name' => $databaseName,
            ]);

            // Send email with login credentials
            Mail::send('emails.tenant-credentials', [
                'tenant' => $tenant,
                'password' => $password,
                'database_name' => $databaseName,
            ], function ($message) use ($tenant) {
                $message->to($tenant->email)
                       ->subject('Your Tenant Account Has Been Approved');
            });

            return redirect()->back()->with('status', 'Tenant application accepted, database created, and credentials sent.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create database: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => 'rejected']);

        return redirect()->back()->with('status', 'Tenant application rejected.');
    }

    public function disable($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => 'disabled']);

        // Send email notification
        Mail::send('emails.tenant-disabled', [
            'tenant' => $tenant,
        ], function ($message) use ($tenant) {
            $message->to($tenant->email)
                   ->subject('Your Tenant Account Has Been Disabled');
        });

        return redirect()->back()->with('status', 'Tenant account has been disabled.');
    }

    public function enable($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => 'accepted']);

        // Send email notification
        Mail::send('emails.tenant-enabled', [
            'tenant' => $tenant,
        ], function ($message) use ($tenant) {
            $message->to($tenant->email)
                   ->subject('Your Tenant Account Has Been Enabled');
        });

        return redirect()->back()->with('status', 'Tenant account has been enabled.');
    }

    public function premium($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['is_premium' => true]);

        // Send email notification
        Mail::send('emails.tenant-premium', [
            'tenant' => $tenant,
        ], function ($message) use ($tenant) {
            $message->to($tenant->email)
                   ->subject('Your Account Has Been Upgraded to Premium');
        });

        return redirect()->back()->with('status', 'Tenant account has been upgraded to premium.');
    }

}
