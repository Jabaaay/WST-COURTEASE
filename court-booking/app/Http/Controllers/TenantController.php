<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\SecondaryAdmin;
use App\Models\Court;
use App\Models\Booking;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Models\TenantAvailability;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Config;


class TenantController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.tenant-register-user');
    }

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
        $password = 'password'; //Str::random(10);
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

            // Set the database connection to the tenant's database
            config(['database.connections.tenant.database' => $databaseName]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Run tenant-specific migrations
            $migrations = [
                '2024_03_22_000000_create_secondary_admins_table.php',
                '2024_03_22_000001_create_users_table.php',
                '2024_03_22_000002_create_courts_table.php',
                '2024_03_22_000003_create_bookings_table.php',
                '2024_03_22_000004_create_tenant_availabilities_table.php'
            ];

            \Log::info('Starting migrations for tenant: ' . $tenant->name);

            foreach ($migrations as $migration) {
                try {
                    \Log::info('Running migration: ' . $migration);
                    
                    $output = Artisan::call('migrate', [
                        '--database' => 'tenant',
                        '--path' => 'database/migrations/tenant/' . $migration,
                        '--force' => true
                    ]);

                    \Log::info('Migration completed: ' . $migration, ['output' => $output]);
                } catch (\Exception $e) {
                    \Log::error('Migration failed: ' . $migration, [
                        'error' => $e->getMessage(),
                        'tenant' => $tenant->name
                    ]);
                    throw $e;
                }
            }

            \Log::info('All migrations completed for tenant: ' . $tenant->name);
            
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
            \Log::error('Tenant acceptance failed', [
                'tenant' => $tenant->name,
                'error' => $e->getMessage()
            ]);
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

        return redirect()->back()->with('success', 'Tenant account has been disabled. and Your Domain is not available anymore.');


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

        return redirect()->back()->with('success', 'Tenant account has been enabled. and Your Domain is available again.');
    }

    public function premium(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        // Validate the request
        $request->validate([
            'plan' => 'required|in:basic,premium,ultimate',
            'domain' => 'required|string|exists:tenants,domain',
            'email' => 'required|email|exists:tenants,email',
        ]);

        // Check if the provided domain and email match the tenant
        if ($request->domain !== $tenant->domain || $request->email !== $tenant->email) {
            return redirect()->back()->with('error', 'Invalid tenant information provided.');
        }

        // Update tenant with plan information
        $tenant->update([
            'is_premium' => true,
            'plan_type' => $request->plan,
            'plan_started_at' => now(),
        ]);

        // Send email notification
        Mail::send('emails.tenant-premium', [
            'tenant' => $tenant,
            'plan' => $request->plan,
        ], function ($message) use ($tenant) {
            $message->to($tenant->email)
                   ->subject('Your Account Has Been Upgraded to ' . ucfirst($tenant->plan_type) . ' Plan');
        });

        return redirect()->back()->with('status', 'Tenant account has been upgraded to ' . ucfirst($request->plan) . ' plan.');
    }

    public function create()
    {
        return view('tenant.create');
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


 
         return view('tenant.dashboard', compact('bookings', 'userCount', 'approvedBookings', 'rejectedBookings', 'allBookings'));
         
    }

    public function secondaryAdmins()
    {
        $secondaryAdmins = SecondaryAdmin::where('tenant_id', session('tenant_id'))->get();
        return view('tenant.secondary-admins.index', compact('secondaryAdmins'));
    }

    public function createSecondaryAdmin()
    {
        return view('tenant.secondary-admins.create');
    }

    public function storeSecondaryAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:secondary_admins,email',
            'role' => 'required|in:sk,secretary,captain',
        ]);

        // check if the email is already in the database
        $secondaryAdmin = SecondaryAdmin::where('email', $request->email)->first();
        if ($secondaryAdmin) {
            return redirect()->back()->with('error', 'Email already exists.');
        }
        

        // Generate a random password
        $password = Str::random(10);

        // Create the secondary admin
        $secondaryAdmin = SecondaryAdmin::create([
            'tenant_id' => session('tenant_id'),
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'role' => $request->role,
        ]);

        // Send email with credentials
        Mail::send('emails.secondary-admin-credentials', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'role' => $request->role,
        ], function ($message) use ($request) {
            $message->to($request->email)
                   ->subject('Your Secondary Admin Account Credentials');
        });

        return redirect()->route('tenant.secondary-admins')
            ->with('success', 'Secondary admin created successfully. Login credentials have been sent to their email.');
    }

    public function editSecondaryAdmin($id)
    {
        $secondaryAdmin = SecondaryAdmin::where('tenant_id', session('tenant_id'))
            ->findOrFail($id);
        return view('tenant.secondary-admins.edit', compact('secondaryAdmin'));
    }

    public function updateSecondaryAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:secondary_admins,email,' . $id,
            'role' => 'required|in:sk,secretary,captain',
        ]);

        $secondaryAdmin = SecondaryAdmin::where('tenant_id', session('tenant_id'))
            ->findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
            $data['password'] = bcrypt($request->password);
        }

        $secondaryAdmin->update($data);

        return redirect()->route('tenant.secondary-admins')
            ->with('success', 'Secondary admin updated successfully.');
    }

    public function destroySecondaryAdmin($id)
    {
        $secondaryAdmin = SecondaryAdmin::where('tenant_id', session('tenant_id'))
            ->findOrFail($id);
        $secondaryAdmin->delete();

        return redirect()->route('tenant.secondary-admins')
            ->with('success', 'Secondary admin deleted successfully.');
    }

    // Users Management
    public function users()
    {
    
      

        $users = TenantUser::all();
        return view('tenant.users.index', compact('users'));
        
        
    }
    

    // Bookings Management
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

        // Get filter status from request
        $status = request()->query('status');

        $query = DB::connection('tenant')
            ->table('bookings')
            ->orderBy('created_at', 'desc');

        // Apply filter if status is provided
        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10);

            

        return view('tenant.bookings.index', compact('bookings'));
    }

    public function acceptBooking($id)
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

            // Update the booking status
            DB::connection('tenant')
                ->table('bookings')
                ->where('id', $id)
                ->update(['status' => 'confirmed']);

            \Log::info('Booking accepted', [
                'booking_id' => $id,
                'tenant_database' => $tenant->database_name
            ]);

            return redirect()->route('tenant.bookings')
                ->with('success', 'Booking accepted successfully.');

        } catch (\Exception $e) {
            \Log::error('Error accepting booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to accept booking. Please try again.');
        }
    }

    public function rejectBooking($id)
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

        // Update the booking status
        DB::connection('tenant')
            ->table('bookings')
            ->where('id', $id)
            ->update(['status' => 'cancelled']);
            


        return redirect()->route('tenant.bookings')
            ->with('success', 'Booking rejected successfully.');
    }

    public function createBooking()
    {
        // $users = User::where('tenant_id', session('tenant_id'))->get();
        // $courts = Court::where('tenant_id', session('tenant_id'))->get();
        // return view('tenant.bookings.create', compact('users', 'courts'));

        return view('tenant.bookings.create');
    }

    public function storeBooking(Request $request)
    {
        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'court_id' => 'required|exists:courts,id',
        //     'start_time' => 'required|date',
        //     'end_time' => 'required|date|after:start_time',
        //     'notes' => 'nullable|string',
        // ]);

        // $court = Court::findOrFail($request->court_id);
        // $hours = Carbon::parse($request->start_time)->diffInHours(Carbon::parse($request->end_time));
        // $total_price = $court->price_per_hour * $hours;

        // Booking::create([
        //     'tenant_id' => session('tenant_id'),
        //     'user_id' => $request->user_id,
        //     'court_id' => $request->court_id,
        //     'start_time' => $request->start_time,
        //     'end_time' => $request->end_time,
        //     'total_price' => $total_price,
        //     'notes' => $request->notes,
        // ]);

        // return redirect()->route('tenant.bookings')
        //     ->with('success', 'Booking created successfully.');

        return redirect()->route('tenant.bookings')
            ->with('success', 'Booking created successfully.');
    }

    public function editBooking($id)
    {
        // $booking = Booking::where('tenant_id', session('tenant_id'))
        //     ->findOrFail($id);
        // $users = User::where('tenant_id', session('tenant_id'))->get();
        // $courts = Court::where('tenant_id', session('tenant_id'))->get();
        // return view('tenant.bookings.edit', compact('booking', 'users', 'courts'));

        return view('tenant.bookings.edit');
    }

    public function updateBooking(Request $request, $id)
    {

        return redirect()->route('tenant.bookings')
            ->with('success', 'Booking updated successfully.');


        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'court_id' => 'required|exists:courts,id',
        //     'start_time' => 'required|date',
        //     'end_time' => 'required|date|after:start_time',
        //     'status' => 'required|in:pending,confirmed,cancelled,completed',
        //     'notes' => 'nullable|string',
        // ]);

        // $booking = Booking::where('tenant_id', session('tenant_id'))
        //     ->findOrFail($id);

        // $court = Court::findOrFail($request->court_id);
        // $hours = Carbon::parse($request->start_time)->diffInHours(Carbon::parse($request->end_time));
        // $total_price = $court->price_per_hour * $hours;

        // $booking->update([
        //     'user_id' => $request->user_id,
        //     'court_id' => $request->court_id,
        //     'start_time' => $request->start_time,
        //     'end_time' => $request->end_time,
        //     'total_price' => $total_price,
        //     'status' => $request->status,
        //     'notes' => $request->notes,
        // ]);

        // return redirect()->route('tenant.bookings')
        //     ->with('success', 'Booking updated successfully.');
    }

    public function deleteBooking($id)
    {
        // $booking = Booking::where('tenant_id', session('tenant_id'))
        //     ->findOrFail($id);
        // $booking->delete();

        // return redirect()->route('tenant.bookings')
        //     ->with('success', 'Booking deleted successfully.');

        return redirect()->route('tenant.bookings')
            ->with('success', 'Booking deleted successfully.');
    }

    // Calendar
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

            return view('tenant.calendar', compact('availabilities', 'bookings'));

        } catch (\Exception $e) {
            \Log::error('Error in calendar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load calendar data. Please try again.');
        }
    }

}
