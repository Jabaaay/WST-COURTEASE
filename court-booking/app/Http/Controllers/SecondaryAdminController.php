<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\SecondaryAdmin;
use App\Models\Booking;
use App\Models\User;

class SecondaryAdminController extends Controller
{
    public function index()
    {
        return view('secondary-admin.dashboard');
    }

    public function logout()
    {
        Auth::guard('secondary-admin')->logout();
        return redirect()->route('tenant.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('secondary-admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('secondary-admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function dashboard()
    {

         // Get the tenant from the domain
         $domain = request()->getHost();
         $domain = str_replace('.localhost', '', $domain);
         
         $tenant = Tenant::where('domain', $domain)
                        ->where('status', 'accepted')
                        ->first();
 
         if (!$tenant) {
             return back()->with('error', 'Invalid tenant domain.');
         }
 
         // Set the tenant's database connection
         Config::set('database.connections.tenant.database', $tenant->database_name);
         DB::purge('tenant');
         DB::reconnect('tenant');

 // format
         $bookings = DB::connection('tenant')
             ->table('bookings')
             ->orderBy('created_at', 'desc')
             ->where('status', 'pending')
             ->get()->take(1);

        $userCount = DB::connection('tenant')
             ->table('users')
             ->count();

        $approvedBookings = DB::connection('tenant')
             ->table('bookings')
             ->where('status', 'confirmed')
             ->count();

         $rejectedBookings = DB::connection('tenant')   
             ->table('bookings')
             ->where('status', 'cancelled')
             ->count();

        $allBookings = DB::connection('tenant')
             ->table('bookings')
             ->count(); 

             
        return view('secondary-admin.dashboard', compact('bookings', 'userCount', 'approvedBookings', 'rejectedBookings', 'allBookings'));


    }

    public function bookings()
    {
         // Get the tenant from the domain
         $domain = request()->getHost();
         $domain = str_replace('.localhost', '', $domain);
         
         $tenant = Tenant::where('domain', $domain)
                        ->where('status', 'accepted')
                        ->first();
 
         if (!$tenant) {
             return back()->with('error', 'Invalid tenant domain.');
         }
 
         // Set the tenant's database connection
         Config::set('database.connections.tenant.database', $tenant->database_name);
         DB::purge('tenant');
         DB::reconnect('tenant');
 
         $bookings = DB::connection('tenant')
             ->table('bookings')
             ->orderBy('created_at', 'desc')
             ->paginate(10);
             
        return view('secondary-admin.bookings.index', compact('bookings'));
    }

    public function calendar()
    {
        try {
            // Get the tenant from the domain
            $domain = request()->getHost();
            $domain = str_replace('.localhost', '', $domain);
            
            $tenant = Tenant::where('domain', $domain)
                           ->where('status', 'accepted')
                           ->first();

            if (!$tenant) {
                return back()->with('error', 'Invalid tenant domain.');
            }

            // Set the tenant's database connection
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Get availabilities for this tenant
            $availabilities = DB::connection('tenant')
                ->table('tenant_availabilities')
                ->where('tenant_id', $tenant->id)
                ->orderBy('start_date', 'asc')
                ->get();

            // Get ALL bookings for this tenant (removed user_id filter)
            $bookings = DB::connection('tenant')
                ->table('bookings')
                ->orderBy('created_at', 'desc')
                ->where('status', 'confirmed')
                ->get();

            \Log::info('Calendar data retrieved', [
                'availabilities_count' => $availabilities->count(),
                'bookings_count' => $bookings->count(),
                'tenant_database' => $tenant->database_name
            ]);

            return view('secondary-admin.calendar.index', compact('availabilities', 'bookings'));

        } catch (\Exception $e) {
            \Log::error('Error in calendar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load calendar data. Please try again.');
        }

    }

    public function users()
    {
        try {
            
            // Get the tenant from the domain
            $domain = request()->getHost();
            $domain = str_replace('.localhost', '', $domain);
            
            $tenant = Tenant::where('domain', $domain)
                           ->where('status', 'accepted')
                           ->first();

            if (!$tenant) {
                return back()->with('error', 'Invalid tenant domain.');
            }

            // Set the tenant's database connection
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');

            $users = DB::connection('tenant')
                ->table('users')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                

            return view('secondary-admin.users.index', compact('users'));


        } catch (\Exception $e) {
            \Log::error('Error in users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load users data. Please try again.');
        }
    }

    public function availability()
    {
        return view('secondary-admin.availability.index');
    }
}
