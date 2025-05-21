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
use App\Models\TenantAvailability;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\SecondaryAdminLoginRequest;

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

    public function store(SecondaryAdminLoginRequest $request)
    {
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

        $pendingBookings = DB::connection('tenant')
             ->table('bookings')
             ->where('status', 'pending')
             ->count();

             
        return view('secondary-admin.dashboard', compact('bookings', 'userCount', 'approvedBookings', 'rejectedBookings', 'allBookings', 'pendingBookings'));


    }

    public function approveBooking($id)
    {
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
        $tenant = Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();
        if (!$tenant) {
            return back()->with('error', 'Invalid tenant domain.');
        }
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::connection('tenant')->table('bookings')->where('id', $id)->update(['status' => 'confirmed']);
        return back()->with('success', 'Booking approved!');
    }

    public function rejectBooking($id)
    {
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
        $tenant = Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();
        if (!$tenant) {
            return back()->with('error', 'Invalid tenant domain.');
        }
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::connection('tenant')->table('bookings')->where('id', $id)->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking rejected!');
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

    public function showBooking($id)
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

            // Get the booking details
            $booking = DB::connection('tenant')
                ->table('bookings')
                ->where('id', $id)
                ->first();

            if (!$booking) {
                return back()->with('error', 'Booking not found.');
            }

            return view('secondary-admin.bookings.show', compact('booking'));

        } catch (\Exception $e) {
            \Log::error('Error showing booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load booking details. Please try again.');
        }
    }

    public function deleteBooking($id)
    {

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

        // Delete the booking
        DB::connection('tenant')
            ->table('bookings')
            ->where('id', $id)
            ->delete();

        return redirect()->route('secondary-admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }

    public function createAvailability()
    {
        return view('secondary-admin.availability.create');
    }

    public function storeAvailability(Request $request)
    {
        // Get tenant using domain from CENTRAL DB
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
    
        $tenant = Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();
    
        if (!$tenant) {
            return back()->with('error', 'Invalid tenant domain.');
        }
    
        // Set up tenant database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
    
        // Validate request
        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
    
        // Get Secondary Admin from CENTRAL DB
        $secondaryAdmin = SecondaryAdmin::find(session('secondary_admin_id'));
    
        if (!$secondaryAdmin) {
            return back()->with('error', 'Secondary admin not found.');
        }
    
        // Optional: Check if secondary admin really belongs to this tenant
        if ($secondaryAdmin->tenant_id != $tenant->id) {
            return back()->with('error', 'Unauthorized access to tenant.');
        }
    
        // Save data in tenant DB
        DB::connection('tenant')->table('tenant_availabilities')->insert([
            'tenant_id' => $tenant->id,
            'event_name' => $request->event_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        \Log::info('Availability created successfully', [
            'tenant_id' => $tenant->id,
            'event_name' => $request->event_name,
        ]);
    
        return redirect()->route('secondary-admin.availability.index')
            ->with('success', 'Availability request submitted successfully.');
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

            \Log::info('Availabilities retrieved', [
                'availabilities_count' => $availabilities->count(),
                'tenant_database' => $tenant->database_name
            ]);

            return view('secondary-admin.availability.index', compact('availabilities'));

        } catch (\Exception $e) {
            \Log::error('Error in availabilities', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load availabilities data. Please try again.');
        }

    }

    public function profile()
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
    
            $secondaryAdmin = SecondaryAdmin::find(session('secondary_admin_id'));
            return view('secondary-admin.profile', compact('secondaryAdmin'));
        
    }

    public function settings()
    {
        return view('secondary-admin.settings');
    }

    public function updateSettings(Request $request)
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

        $secondaryAdmin = SecondaryAdmin::find(session('secondary_admin_id'));

        if ($request->has('current_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $secondaryAdmin->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $secondaryAdmin->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('secondary-admin.settings')->with('success', 'Password updated successfully.');
        }

        if ($request->has('email_notifications') || $request->has('sms_notifications')) {
            $secondaryAdmin->update([
                'email_notifications' => $request->has('email_notifications'),
                'sms_notifications' => $request->has('sms_notifications')
            ]);

            return redirect()->route('secondary-admin.settings')->with('success', 'Notification preferences updated successfully.');
        }

        return back()->with('error', 'No changes were made.');
    }
    
    
    
}
